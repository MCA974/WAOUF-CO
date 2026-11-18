<?php

namespace App\Tests;

use App\Entity\Article;
use App\Entity\Commande;
use App\Repository\ArticleRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeFlowTest extends WebTestCase
{
    public function testFluxCommandeAvecAnimal(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        /** @var ArticleRepository $articleRepo */
        $articleRepo = $container->get(ArticleRepository::class);

        $rocky = $articleRepo->findOneBy(['urlSimplifiee' => 'rocky-golden-retriever']);
        $this->assertNotNull($rocky);
        $rocky->setStock(1);
        $rocky->setDisponible(true);
        $em->flush();
        $stockInitial = 1;

        // 1. Ajouter Rocky au panier
        $client->request('GET', '/panier/ajouter/' . $rocky->getId());
        $this->assertResponseRedirects('/panier');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 2. Aller sur la page de validation
        $crawler = $client->request('GET', '/commande/valider');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'VALIDATION_ADOPTION');

        // 3. Soumettre la commande
        $client->request('POST', '/commande/valider', [
            'nom_client' => 'Jean Testeur',
            'email_client' => 'jean@test.com',
            'telephone_client' => '0600000000',
            'adresse_livraison' => '10 rue des Chiens Heureux, 75001 Paris',
        ]);

        $this->assertResponseRedirects();
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 4. Vérifier la confirmation
        $this->assertSelectorTextContains('h2', 'WAOUF-2026-');
        $this->assertSelectorTextContains('body', 'VALIDATION ADOPTION');

        // 5. Vérifier en base de données
        /** @var CommandeRepository $cmdRepo */
        $cmdRepo = $container->get(CommandeRepository::class);
        $commande = $cmdRepo->findOneBy([], ['id' => 'DESC']);
        $this->assertNotNull($commande);
        $this->assertSame(Commande::STATUT_VALIDATION_ADOPTION, $commande->getStatut());

        // Stock décrémenté
        $articleApres = $articleRepo->find($rocky->getId());
        $this->assertSame($stockInitial - 1, $articleApres->getStock());
    }
}
