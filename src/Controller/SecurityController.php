<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Intercepté par le firewall de Symfony.');
    }

    #[Route('/mon-compte', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function account(CommandeRepository $commandeRepository): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        $commandes = $commandeRepository->findBy(
            ['utilisateur' => $user],
            ['dateCreation' => 'DESC']
        );

        $adoptions = [];
        foreach ($commandes as $cmd) {
            foreach ($cmd->getLignes() as $ligne) {
                if ($ligne->getArticle()?->getAnimal() !== null) {
                    $adoptions[] = [
                        'commande' => $cmd,
                        'ligne' => $ligne,
                        'animal' => $ligne->getArticle()->getAnimal(),
                    ];
                }
            }
        }

        return $this->render('security/account.html.twig', [
            'user' => $user,
            'commandes' => $commandes,
            'adoptions' => $adoptions,
        ]);
    }
}
