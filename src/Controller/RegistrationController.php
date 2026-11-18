<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository,
        Security $security
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        if ($request->isMethod('POST')) {
            $email = trim((string) $request->request->get('email'));
            $motDePasse = (string) $request->request->get('password');
            $prenom = trim((string) $request->request->get('prenom'));
            $nom = trim((string) $request->request->get('nom'));
            $telephone = trim((string) $request->request->get('telephone'));
            $adresse = trim((string) $request->request->get('adresse'));

            if (empty($email) || empty($motDePasse) || empty($prenom) || empty($nom)) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->render('security/register.html.twig');
            }

            if ($utilisateurRepository->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
                return $this->render('security/register.html.twig');
            }

            $user = new Utilisateur();
            $user->setEmail($email);
            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setTelephone($telephone ?: 'Non renseigné');
            $user->setAdresse($adresse ?: 'Non renseignée');
            $user->setRoles(['ROLE_USER']);
            $user->setMotDePasse($userPasswordHasher->hashPassword($user, $motDePasse));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Bienvenue chez WAOUF & CO ! Votre compte a été créé avec succès.');

            // Authentification automatique
            return $security->login($user, 'form_login', 'main') ?: $this->redirectToRoute('app_account');
        }

        return $this->render('security/register.html.twig');
    }
}
