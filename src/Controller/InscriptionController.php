<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\Situation;
use App\Entity\Utilisateur;
use App\Form\AvatarType;
use App\Form\SituationType;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * Étape 1: créer le compte utilisateur
     */
    #[Route('/inscription/utilisateur', name: 'inscription_utilisateur')]
    public function utilisateur(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($utilisateur);
            $em->flush();

        }

        return $this->render('inscription/utilisateur.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Étape 2: créer la situation liée à l'utilisateur
     */
    #[Route('/inscription/profil/{id}', name: 'inscription_profil')]
    public function profil(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // on récupère l'utilisateur créé à l'étape 1
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        // on prépare les deux objets
        $situation = new Situation();
        $avatar = new Avatar();

        // on crée les deux formulaires
        $situationForm = $this->createForm(SituationType::class, $situation);
        $avatarForm = $this->createForm(AvatarType::class, $avatar);

        // on fait lire la requête aux 2
        $situationForm->handleRequest($request);
        $avatarForm->handleRequest($request);

        // on vérifie si le bouton a été envoyé
        if ($situationForm->isSubmitted() && $avatarForm->isSubmitted()
            && $situationForm->isValid() && $avatarForm->isValid()) {

            // on relie les 2 à l'utilisateur
            $situation->setUtilisateur($utilisateur);
            $avatar->setUtilisateur($utilisateur);

            $em->persist($situation);
            $em->persist($avatar);
            $em->flush();

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('inscription/profil.html.twig', [
            'utilisateur' => $utilisateur,
            'situationForm' => $situationForm->createView(),
            'avatarForm' => $avatarForm->createView(),
        ]);
    }


    /**
     * Étape 3: créer l'avatar
     */
    #[Route('/inscription/avatar/{id}', name: 'inscription_avatar')]
    public function avatar(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $avatar = new Avatar();

        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatar->setUtilisateur($utilisateur);

            $em->persist($avatar);
            $em->flush();

            // ici on affiche la page "profil créé" à construire !
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('inscription/avatar.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }
}
