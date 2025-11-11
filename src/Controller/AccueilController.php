<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/welcome', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('accueil/welcome.html.twig');
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        Security $security
    ): Response {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. hasher le mot de passe
            $plainPassword = $utilisateur->getMotDePasse();
            $hashedPassword = $hasher->hashPassword($utilisateur, $plainPassword);
            $utilisateur->setMotDePasse($hashedPassword);

            // 2. sauver
            $em->persist($utilisateur);
            $em->flush();

            // 3. auto-login
            $security->login($utilisateur);

            // 4. rediriger vers accueil ou profil
            return $this->redirectToRoute('inscription_profil', [
                'id' => $utilisateur->getId(),
            ]);
                
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
