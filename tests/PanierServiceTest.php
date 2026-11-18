<?php

namespace App\Tests;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class PanierServiceTest extends KernelTestCase
{
    private PanierService $panierService;
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        // Créer et attacher une fausse session dans la RequestStack pour les tests
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);
        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);
        $requestStack->push($request);

        $this->panierService = $container->get(PanierService::class);
        $this->articleRepository = $container->get(ArticleRepository::class);
        
        // S'assurer que les articles sont disponibles pour les tests
        $em = $container->get('doctrine.orm.entity_manager');
        $rocky = $this->articleRepository->findOneBy(['urlSimplifiee' => 'rocky-golden-retriever']);
        if ($rocky) {
            $rocky->setStock(1);
            $rocky->setDisponible(true);
            $em->flush();
        }

        $this->panierService->vider();
    }

    public function testAjoutArticleClassiqueEtCalculs(): void
    {
        $croquettes = $this->articleRepository->findOneBy(['urlSimplifiee' => 'croquettes-saumon-patate-douce']);
        $this->assertNotNull($croquettes);

        // Ajouter 1 sac de croquettes (4290 centimes = 42,90 €)
        $res = $this->panierService->ajouter($croquettes->getId(), null, 1);
        $this->assertTrue($res['success']);

        $totaux = $this->panierService->getTotaux();
        $this->assertSame(1, $totaux['nombreArticles']);
        $this->assertSame(4290, $totaux['sousTotalTTC']);
        // Sous-total < 5000 centimes => frais de port = 490 centimes
        $this->assertSame(490, $totaux['fraisPort']);
        $this->assertFalse($totaux['fraisPortOfferts']);
        $this->assertSame(4290 + 490, $totaux['totalTTC']);

        // Appliquer code promo WAOUF10 (-10%)
        $promoRes = $this->panierService->appliquerPromo('WAOUF10');
        $this->assertTrue($promoRes['success']);

        $totauxPromo = $this->panierService->getTotaux();
        // Remise = 10% de 4290 = 429 centimes
        $this->assertSame(429, $totauxPromo['montantRemise']);
        // Total = (4290 - 429) + 490 = 3861 + 490 = 4351
        $this->assertSame(3861 + 490, $totauxPromo['totalTTC']);
    }

    public function testFraisDePortOffertsAuDelaDe50Euros(): void
    {
        $croquettes = $this->articleRepository->findOneBy(['urlSimplifiee' => 'croquettes-saumon-patate-douce']);
        $this->assertNotNull($croquettes);

        // Ajouter 2 sacs de croquettes (2 * 4290 = 8580 centimes > 5000)
        $this->panierService->ajouter($croquettes->getId(), null, 2);

        $totaux = $this->panierService->getTotaux();
        $this->assertSame(2, $totaux['nombreArticles']);
        $this->assertSame(8580, $totaux['sousTotalTTC']);
        // Livraison offerte !
        $this->assertSame(0, $totaux['fraisPort']);
        $this->assertTrue($totaux['fraisPortOfferts']);
        $this->assertSame(8580, $totaux['totalTTC']);
    }

    public function testRegleMetierAnimalMaxUn(): void
    {
        $rocky = $this->articleRepository->findOneBy(['urlSimplifiee' => 'rocky-golden-retriever']);
        $this->assertNotNull($rocky);
        $this->assertNotNull($rocky->getAnimal());

        // Premier ajout
        $res1 = $this->panierService->ajouter($rocky->getId());
        $this->assertTrue($res1['success']);

        // Deuxième ajout (doit rester strictement bloqué à 1 !)
        $res2 = $this->panierService->ajouter($rocky->getId(), null, 2);
        $this->assertTrue($res2['success']);

        $totaux = $this->panierService->getTotaux();
        $this->assertSame(1, $totaux['nombreArticles']);
        $this->assertTrue($totaux['hasAnimal']);
    }
}
