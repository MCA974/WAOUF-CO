<?php

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    public const CODE_PROMO_VALIDE = 'WAOUF10';
    public const TAUX_PROMO = 0.10; // -10%
    public const FRAIS_PORT_BASE = 490; // 4,90 € en centimes
    public const SEUIL_PORT_OFFERT = 5000; // 50,00 € en centimes

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ArticleRepository $articleRepository
    ) {
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function getCle(int $articleId, ?string $option = null): string
    {
        $optClean = $option ? trim($option) : '';
        return $articleId . '_' . $optClean;
    }

    public function ajouter(int $articleId, ?string $option = null, int $quantite = 1): array
    {
        $session = $this->getSession();
        $panier = $session->get('panier', []);
        $article = $this->articleRepository->find($articleId);

        if (!$article || !$article->isDisponible() || $article->getStock() <= 0) {
            return [
                'success' => false,
                'message' => "Cet article n'est pas disponible actuellement.",
            ];
        }

        $cle = $this->getCle($articleId, $option);
        $isAnimal = $article->getAnimal() !== null;

        if ($isAnimal) {
            // Règle d'or : Strictement bloqué à 1 pour les adoptions d'animaux
            $panier[$cle] = [
                'article_id' => $articleId,
                'option' => null,
                'quantite' => 1,
            ];
            $session->set('panier', $panier);

            return [
                'success' => true,
                'message' => sprintf("%s a été ajouté à votre dossier d'adoption (limité à 1 par foyer).", $article->getNom()),
            ];
        }

        $qteActuelle = isset($panier[$cle]) ? (int) $panier[$cle]['quantite'] : 0;
        $nouvelleQte = $qteActuelle + $quantite;

        if ($nouvelleQte > $article->getStock()) {
            $nouvelleQte = $article->getStock();
            $message = sprintf("Stock maximum atteint pour %s (%d max).", $article->getNom(), $article->getStock());
        } else {
            $message = sprintf("%s a été ajouté à votre panier !", $article->getNom());
        }

        $panier[$cle] = [
            'article_id' => $articleId,
            'option' => $option ? trim($option) : null,
            'quantite' => max(1, $nouvelleQte),
        ];

        $session->set('panier', $panier);

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    public function diminuer(string $cle): void
    {
        $session = $this->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$cle])) {
            if ($panier[$cle]['quantite'] > 1) {
                $panier[$cle]['quantite']--;
            } else {
                unset($panier[$cle]);
            }
            $session->set('panier', $panier);
        }
    }

    public function supprimer(string $cle): void
    {
        $session = $this->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$cle])) {
            unset($panier[$cle]);
            $session->set('panier', $panier);
        }
    }

    public function appliquerPromo(string $code): array
    {
        $session = $this->getSession();
        $cleanCode = strtoupper(trim($code));

        if ($cleanCode === self::CODE_PROMO_VALIDE) {
            $session->set('code_promo', self::CODE_PROMO_VALIDE);
            return [
                'success' => true,
                'message' => "Youpi ! Code WAOUF10 appliqué : -10% sur votre commande !",
            ];
        }

        return [
            'success' => false,
            'message' => "Le code promo '" . $code . "' est invalide. Astuce : essayez WAOUF10 !",
        ];
    }

    public function retirerPromo(): void
    {
        $this->getSession()->remove('code_promo');
    }

    public function vider(): void
    {
        $this->getSession()->remove('panier');
        $this->getSession()->remove('code_promo');
    }

    public function getContenu(): array
    {
        $session = $this->getSession();
        $panier = $session->get('panier', []);
        $elements = [];

        foreach ($panier as $cle => $item) {
            $article = $this->articleRepository->find($item['article_id']);
            if (!$article) {
                unset($panier[$cle]);
                $session->set('panier', $panier);
                continue;
            }

            $prixUnitaire = $article->getPrix();
            $quantite = (int) $item['quantite'];
            $totalLigne = $prixUnitaire * $quantite;

            $elements[$cle] = [
                'cle' => $cle,
                'article' => $article,
                'option' => $item['option'] ?? null,
                'quantite' => $quantite,
                'prixUnitaire' => $prixUnitaire,
                'totalLigne' => $totalLigne,
                'isAnimal' => $article->getAnimal() !== null,
            ];
        }

        return $elements;
    }

    public function getTotaux(): array
    {
        $elements = $this->getContenu();
        $sousTotalTTC = 0;
        $nbArticles = 0;
        $hasAnimal = false;

        foreach ($elements as $item) {
            $sousTotalTTC += $item['totalLigne'];
            $nbArticles += $item['quantite'];
            if ($item['isAnimal']) {
                $hasAnimal = true;
            }
        }

        $sousTotalHT = (int) round($sousTotalTTC / 1.20);
        $tva = $sousTotalTTC - $sousTotalHT;

        // Code Promo
        $codePromo = $this->getSession()->get('code_promo');
        $montantRemise = 0;
        if ($codePromo === self::CODE_PROMO_VALIDE && $sousTotalTTC > 0) {
            $montantRemise = (int) round($sousTotalTTC * self::TAUX_PROMO);
        }

        $sousTotalApresRemise = max(0, $sousTotalTTC - $montantRemise);

        // Frais de port : 490 centimes (4,90 €), offerts si >= 5000 centimes (50,00 €)
        if ($sousTotalTTC === 0) {
            $fraisPort = 0;
        } elseif ($sousTotalTTC >= self::SEUIL_PORT_OFFERT) {
            $fraisPort = 0;
        } else {
            $fraisPort = self::FRAIS_PORT_BASE;
        }

        $totalTTC = $sousTotalApresRemise + $fraisPort;

        return [
            'nombreArticles' => $nbArticles,
            'sousTotalTTC' => $sousTotalTTC,
            'sousTotalHT' => $sousTotalHT,
            'tva' => $tva,
            'codePromo' => $codePromo,
            'montantRemise' => $montantRemise,
            'fraisPort' => $fraisPort,
            'fraisPortOfferts' => ($sousTotalTTC >= self::SEUIL_PORT_OFFERT && $sousTotalTTC > 0),
            'restePourPortOffert' => max(0, self::SEUIL_PORT_OFFERT - $sousTotalTTC),
            'totalTTC' => $totalTTC,
            'hasAnimal' => $hasAnimal,
        ];
    }
}
