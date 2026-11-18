<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('', name: 'app_panier', methods: ['GET'])]
    public function index(PanierService $panierService): Response
    {
        return $this->render('panier/index.html.twig', [
            'elements' => $panierService->getContenu(),
            'totaux' => $panierService->getTotaux(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['GET', 'POST'])]
    public function ajouter(
        int $id,
        Request $request,
        PanierService $panierService
    ): Response {
        $option = $request->query->get('option') ?? $request->request->get('option');
        $quantite = (int) ($request->query->get('quantite') ?? $request->request->get('quantite', 1));
        if ($quantite < 1) {
            $quantite = 1;
        }

        $result = $panierService->ajouter($id, $option, $quantite);

        if ($result['success']) {
            $this->addFlash('success', $result['message']);
        } else {
            $this->addFlash('error', $result['message']);
        }

        if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
            $totaux = $panierService->getTotaux();
            $drawerHtml = $this->renderView('panier/_drawer_content.html.twig', [
                'elements' => $panierService->getContenu(),
                'totaux' => $totaux,
            ]);

            return new JsonResponse([
                'success' => $result['success'],
                'message' => $result['message'],
                'count' => $totaux['nombreArticles'],
                'total' => $totaux['totalTTC'],
                'drawerHtml' => $drawerHtml,
            ]);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_panier'));
    }

    #[Route('/diminuer/{cle}', name: 'app_panier_diminuer', methods: ['GET', 'POST'])]
    public function diminuer(string $cle, Request $request, PanierService $panierService): Response
    {
        $panierService->diminuer($cle);

        if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
            $totaux = $panierService->getTotaux();
            return new JsonResponse([
                'success' => true,
                'count' => $totaux['nombreArticles'],
                'total' => $totaux['totalTTC'],
                'drawerHtml' => $this->renderView('panier/_drawer_content.html.twig', [
                    'elements' => $panierService->getContenu(),
                    'totaux' => $totaux,
                ]),
            ]);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_panier'));
    }

    #[Route('/supprimer/{cle}', name: 'app_panier_supprimer', methods: ['GET', 'POST'])]
    public function supprimer(string $cle, Request $request, PanierService $panierService): Response
    {
        $panierService->supprimer($cle);
        $this->addFlash('info', 'Article retiré du panier.');

        if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
            $totaux = $panierService->getTotaux();
            return new JsonResponse([
                'success' => true,
                'count' => $totaux['nombreArticles'],
                'total' => $totaux['totalTTC'],
                'drawerHtml' => $this->renderView('panier/_drawer_content.html.twig', [
                    'elements' => $panierService->getContenu(),
                    'totaux' => $totaux,
                ]),
            ]);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_panier'));
    }

    #[Route('/promo', name: 'app_panier_promo', methods: ['POST'])]
    public function promo(Request $request, PanierService $panierService): Response
    {
        $code = (string) $request->request->get('code_promo', '');
        $result = $panierService->appliquerPromo($code);

        if ($result['success']) {
            $this->addFlash('success', $result['message']);
        } else {
            $this->addFlash('error', $result['message']);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_panier'));
    }

    #[Route('/promo/retirer', name: 'app_panier_promo_retirer', methods: ['GET', 'POST'])]
    public function retirerPromo(Request $request, PanierService $panierService): Response
    {
        $panierService->retirerPromo();
        $this->addFlash('info', 'Code promo retiré.');

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_panier'));
    }

    #[Route('/tiroir', name: 'app_panier_drawer', methods: ['GET'])]
    public function drawer(PanierService $panierService): Response
    {
        return $this->render('panier/_drawer_content.html.twig', [
            'elements' => $panierService->getContenu(),
            'totaux' => $panierService->getTotaux(),
        ]);
    }
}
