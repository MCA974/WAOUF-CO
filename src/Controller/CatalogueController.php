<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/', name: 'app_catalogue', methods: ['GET'])]
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        $categorieUrl = $request->query->get('categorie');
        $tri = $request->query->get('tri');

        $categories = $categorieRepository->findAll();
        $categorieActive = null;

        $qb = $articleRepository->createQueryBuilder('a')
            ->leftJoin('a.categorie', 'c')
            ->addSelect('c')
            ->leftJoin('a.animal', 'an')
            ->addSelect('an')
            ->where('a.disponible = :dispo')
            ->setParameter('dispo', true);

        if ($categorieUrl) {
            $categorieActive = $categorieRepository->findOneBy(['urlSimplifiee' => $categorieUrl]);
            if ($categorieActive) {
                $qb->andWhere('a.categorie = :categorie')
                    ->setParameter('categorie', $categorieActive);
            }
        }

        if ($q !== '') {
            $qb->andWhere('LOWER(a.nom) LIKE :search OR LOWER(a.description) LIKE :search')
                ->setParameter('search', '%' . strtolower($q) . '%');
        }

        if ($tri === 'prix_asc') {
            $qb->orderBy('a.prix', 'ASC');
        } elseif ($tri === 'prix_desc') {
            $qb->orderBy('a.prix', 'DESC');
        } else {
            $qb->orderBy('a.id', 'DESC');
        }

        $articles = $qb->getQuery()->getResult();

        return $this->render('catalogue/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'categorieActive' => $categorieActive,
            'q' => $q,
            'tri' => $tri,
        ]);
    }

    #[Route('/article/{urlSimplifiee}', name: 'app_article_detail', methods: ['GET'])]
    public function detail(string $urlSimplifiee, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['urlSimplifiee' => $urlSimplifiee]);

        if (!$article) {
            throw $this->createNotFoundException("Cet article n'existe pas ou n'est plus disponible.");
        }

        // Suggestions de la même catégorie
        $similaires = $articleRepository->createQueryBuilder('a')
            ->where('a.categorie = :cat')
            ->andWhere('a.id != :id')
            ->andWhere('a.disponible = true')
            ->setParameter('cat', $article->getCategorie())
            ->setParameter('id', $article->getId())
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return $this->render('catalogue/detail.html.twig', [
            'article' => $article,
            'similaires' => $similaires,
        ]);
    }
}
