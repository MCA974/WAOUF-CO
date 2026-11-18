<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Repository\AnimalRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(
        ArticleRepository $articleRepository,
        CategorieRepository $categorieRepository,
        CommandeRepository $commandeRepository,
        AnimalRepository $animalRepository
    ): Response {
        $articles = $articleRepository->findBy([], ['id' => 'DESC'], 6);
        $commandes = $commandeRepository->findBy([], ['dateCreation' => 'DESC'], 6);
        $categories = $categorieRepository->findAll();

        $totalVentes = 0;
        $totalCommandes = $commandeRepository->count([]);
        $allCmds = $commandeRepository->findAll();
        foreach ($allCmds as $cmd) {
            $totalVentes += $cmd->getMontantTotal();
        }

        return $this->render('admin/dashboard.html.twig', [
            'totalArticles' => $articleRepository->count([]),
            'totalAnimaux' => $animalRepository->count([]),
            'totalCommandes' => $totalCommandes,
            'totalVentes' => $totalVentes,
            'articles' => $articles,
            'commandes' => $commandes,
            'categories' => $categories,
        ]);
    }

    // ==========================================
    // GESTION DES ARTICLES & ANIMAUX
    // ==========================================

    #[Route('/articles', name: 'app_admin_articles', methods: ['GET'])]
    public function articles(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/articles.html.twig', [
            'articles' => $articleRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/articles/nouveau', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function newArticle(
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $categories = $categorieRepository->findAll();

        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom'));
            $prixEuros = (float) $request->request->get('prix');
            $stock = (int) $request->request->get('stock', 0);
            $description = trim((string) $request->request->get('description'));
            $categorieId = (int) $request->request->get('categorie_id');
            $image = trim((string) $request->request->get('image'));
            $badge = trim((string) $request->request->get('badge')) ?: null;
            $optionsRaw = trim((string) $request->request->get('options'));
            $isAnimal = (bool) $request->request->get('is_animal');

            $slugger = new AsciiSlugger();
            $urlSimplifiee = strtolower($slugger->slug($nom)->toString()) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);

            $categorie = $categorieRepository->find($categorieId);
            if (!$categorie) {
                $this->addFlash('error', 'Catégorie invalide.');
                return $this->redirectToRoute('app_admin_article_new');
            }

            $article = new Article();
            $article->setNom($nom);
            $article->setUrlSimplifiee($urlSimplifiee);
            $article->setDescription($description);
            $article->setPrix((int) round($prixEuros * 100));
            $article->setStock($stock);
            $article->setImage($image ?: 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=800&q=80');
            $article->setBadge($badge);
            $article->setCategorie($categorie);
            $article->setDisponible(true);

            if ($optionsRaw) {
                $opts = array_filter(array_map('trim', explode(',', $optionsRaw)));
                $article->setOptions(!empty($opts) ? array_values($opts) : null);
            }

            if ($isAnimal) {
                $animal = new Animal();
                $animal->setEspece((string) $request->request->get('espece', 'Chien'));
                $animal->setRace((string) $request->request->get('race', 'Croisé'));
                $animal->setSexe((string) $request->request->get('sexe', 'Mâle'));
                $dateN = new \DateTime((string) $request->request->get('date_naissance', 'now'));
                $animal->setDateNaissance($dateN);
                $animal->setNumeroPuce((string) $request->request->get('numero_puce', '250' . random_int(100000000000, 999999999999)));
                $animal->setVaccine((bool) $request->request->get('vaccine'));
                $animal->setSterilise((bool) $request->request->get('sterilise'));

                $animal->setArticle($article);
                $article->setAnimal($animal);
                $entityManager->persist($animal);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/article_form.html.twig', [
            'categories' => $categories,
            'article' => null,
        ]);
    }

    #[Route('/articles/editer/{id}', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function editArticle(
        Article $article,
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $categories = $categorieRepository->findAll();

        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom'));
            $prixEuros = (float) $request->request->get('prix');
            $stock = (int) $request->request->get('stock', 0);
            $description = trim((string) $request->request->get('description'));
            $categorieId = (int) $request->request->get('categorie_id');
            $image = trim((string) $request->request->get('image'));
            $badge = trim((string) $request->request->get('badge')) ?: null;
            $optionsRaw = trim((string) $request->request->get('options'));
            $disponible = (bool) $request->request->get('disponible');

            $categorie = $categorieRepository->find($categorieId);
            if ($categorie) {
                $article->setCategorie($categorie);
            }

            $article->setNom($nom);
            $article->setDescription($description);
            $article->setPrix((int) round($prixEuros * 100));
            $article->setStock($stock);
            $article->setImage($image);
            $article->setBadge($badge);
            $article->setDisponible($disponible);

            if ($optionsRaw) {
                $opts = array_filter(array_map('trim', explode(',', $optionsRaw)));
                $article->setOptions(!empty($opts) ? array_values($opts) : null);
            } else {
                $article->setOptions(null);
            }

            // Si fiche animal
            if ($article->getAnimal()) {
                $animal = $article->getAnimal();
                $animal->setEspece((string) $request->request->get('espece'));
                $animal->setRace((string) $request->request->get('race'));
                $animal->setSexe((string) $request->request->get('sexe'));
                $dateStr = (string) $request->request->get('date_naissance');
                if ($dateStr) {
                    $animal->setDateNaissance(new \DateTime($dateStr));
                }
                $animal->setNumeroPuce((string) $request->request->get('numero_puce'));
                $animal->setVaccine((bool) $request->request->get('vaccine'));
                $animal->setSterilise((bool) $request->request->get('sterilise'));
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article mis à jour avec succès !');
            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/article_form.html.twig', [
            'categories' => $categories,
            'article' => $article,
        ]);
    }

    #[Route('/articles/supprimer/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    public function deleteArticle(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete_article_' . $article->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success', 'Article supprimé.');
        }

        return $this->redirectToRoute('app_admin_articles');
    }

    // ==========================================
    // GESTION DES CATÉGORIES
    // ==========================================

    #[Route('/categories', name: 'app_admin_categories', methods: ['GET', 'POST'])]
    public function categories(
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom'));
            $couleur = trim((string) $request->request->get('couleurHex', '#FF6B6B'));

            if (!empty($nom)) {
                $slugger = new AsciiSlugger();
                $url = strtolower($slugger->slug($nom)->toString());

                $cat = new Categorie();
                $cat->setNom($nom);
                $cat->setUrlSimplifiee($url);
                $cat->setCouleurHex($couleur);

                $entityManager->persist($cat);
                $entityManager->flush();

                $this->addFlash('success', 'Catégorie créée !');
            }

            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    // ==========================================
    // GESTION DES COMMANDES & STATUTS
    // ==========================================

    #[Route('/commandes', name: 'app_admin_commandes', methods: ['GET'])]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/commandes.html.twig', [
            'commandes' => $commandeRepository->findBy([], ['dateCreation' => 'DESC']),
        ]);
    }

    #[Route('/commandes/statut/{id}', name: 'app_admin_commande_statut', methods: ['POST'])]
    public function changeStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $nouveauStatut = (string) $request->request->get('statut');
        $statutsValides = [
            Commande::STATUT_EN_ATTENTE,
            Commande::STATUT_PAYEE,
            Commande::STATUT_VALIDATION_ADOPTION,
            Commande::STATUT_EXPEDIEE,
        ];

        if (in_array($nouveauStatut, $statutsValides, true)) {
            $commande->setStatut($nouveauStatut);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le statut de la commande %s a été mis à jour en %s.', $commande->getReference(), $nouveauStatut));
        }

        return $this->redirectToRoute('app_admin_commandes');
    }
}
