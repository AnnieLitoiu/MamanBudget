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
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_welcome');
    }

    #[Route('/welcome', name: 'app_welcome')]
    public function index(): Response
    {
        return $this->render('accueil/welcome.html.twig');
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
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
            // Vérifier si l'email existe déjà
            $existingUser = $em->getRepository(Utilisateur::class)
                ->findOneBy(['email' => $utilisateur->getEmail()]);

            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé. Veuillez en choisir un autre.');
                return $this->render('security/register.html.twig', [
                    'form' => $form,
                ]);
            }

            $hashed = $hasher->hashPassword($utilisateur, $utilisateur->getMotDePasse());
            $utilisateur->setMotDePasse($hashed);

            $em->persist($utilisateur);
            $em->flush();

            // auto-login puis retour vers /welcome
            $security->login($utilisateur);

            return $this->redirectToRoute('app_menu');
        }


        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
