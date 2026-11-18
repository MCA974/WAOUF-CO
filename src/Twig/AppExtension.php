<?php

namespace App\Twig;

use App\Service\PanierService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly PanierService $panierService
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('prix_euro', [$this, 'formatPrixEuro']),
        ];
    }

    public function formatPrixEuro(int|float|null $centimes): string
    {
        if ($centimes === null) {
            return '0,00 €';
        }

        $euros = $centimes / 100;
        return number_format($euros, 2, ',', ' ') . ' €';
    }

    public function getGlobals(): array
    {
        try {
            $totaux = $this->panierService->getTotaux();
            return [
                'panier_count' => $totaux['nombreArticles'],
                'panier_total' => $totaux['totalTTC'],
            ];
        } catch (\Throwable) {
            return [
                'panier_count' => 0,
                'panier_total' => 0,
            ];
        }
    }
}
