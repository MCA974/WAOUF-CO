<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Utilisateur;
use App\Repository\ArticleRepository;
use App\Repository\CommandeRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/valider', name: 'app_commande_valider', methods: ['GET', 'POST'])]
    public function valider(
        Request $request,
        PanierService $panierService,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $elements = $panierService->getContenu();
        $totaux = $panierService->getTotaux();

        if (empty($elements)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_catalogue');
        }

        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $adresse = trim((string) $request->request->get('adresse_livraison', ''));
            $nomClient = trim((string) $request->request->get('nom_client', ''));
            $emailClient = trim((string) $request->request->get('email_client', ''));
            $telephoneClient = trim((string) $request->request->get('telephone_client', ''));

            if (empty($adresse)) {
                $this->addFlash('error', 'Veuillez renseigner une adresse de livraison valide.');
                return $this->render('commande/valider.html.twig', [
                    'elements' => $elements,
                    'totaux' => $totaux,
                    'user' => $user,
                ]);
            }

            // 1. Vérification stricte des stocks
            foreach ($elements as $cle => $item) {
                $article = $articleRepository->find($item['article']->getId());
                if (!$article || !$article->isDisponible() || $article->getStock() < $item['quantite']) {
                    $this->addFlash('error', sprintf('Désolé, l\'article "%s" n\'a plus assez de stock disponible.', $item['article']->getNom()));
                    return $this->redirectToRoute('app_panier');
                }
            }

            // 2. Création de la Commande
            $commande = new Commande();
            $reference = 'WAOUF-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $commande->setReference($reference);
            $commande->setDateCreation(new \DateTimeImmutable());
            $commande->setMontantTotal($totaux['totalTTC']);
            $commande->setFraisLivraison($totaux['fraisPort']);
            $commande->setCodePromo($totaux['codePromo']);
            
            $adresseComplete = $adresse;
            if (!$user && $nomClient !== '') {
                $adresseComplete = sprintf("%s (%s - %s)\n%s", $nomClient, $emailClient, $telephoneClient, $adresse);
            }
            $commande->setAdresseLivraison($adresseComplete);

            if ($user instanceof Utilisateur) {
                $commande->setUtilisateur($user);
            }

            $hasAnimal = false;

            // 3. Décrémentation des stocks et création des lignes
            foreach ($elements as $item) {
                $article = $articleRepository->find($item['article']->getId());
                
                // Décrémentation stock
                $article->setStock($article->getStock() - $item['quantite']);
                if ($article->getStock() <= 0 && $article->getAnimal() !== null) {
                    $article->setDisponible(false); // Animal adopté !
                }

                if ($article->getAnimal() !== null) {
                    $hasAnimal = true;
                }

                $ligne = new LigneCommande();
                $ligne->setCommande($commande);
                $ligne->setArticle($article);
                $ligne->setQuantite($item['quantite']);
                $ligne->setPrixUnitaire($article->getPrix());
                $ligne->setOptionChoisie($item['option']);

                $commande->addLigne($ligne);
            }

            // Statut selon présence d'un animal
            if ($hasAnimal) {
                $commande->setStatut(Commande::STATUT_VALIDATION_ADOPTION);
            } else {
                $commande->setStatut(Commande::STATUT_PAYEE);
            }

            $entityManager->persist($commande);
            $entityManager->flush();

            // 4. Vider le panier
            $panierService->vider();

            $this->addFlash('success', 'Votre commande a été validée avec succès !');

            return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
        }

        return $this->render('commande/valider.html.twig', [
            'elements' => $elements,
            'totaux' => $totaux,
            'user' => $user,
        ]);
    }

    #[Route('/confirmation/{id}', name: 'app_commande_confirmation', methods: ['GET'])]
    public function confirmation(Commande $commande): Response
    {
        return $this->render('commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
}
