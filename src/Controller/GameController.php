<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Utilisateur;
use App\Service\GameEngine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GameEngine $engine
    ) {}

    #[Route('/game/play', name: 'game_play', methods: ['GET'])]
    public function play(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session      = $request->getSession();
        $composition  = $session->get('compositionFamiliale'); // bebe|ado|les_deux
        $logement     = $session->get('logement');
        $situationPro = $session->get('situationPro');

        if (!$composition) {
            $this->addFlash('warning', 'Choisis d’abord ta situation familiale.');
            return $this->redirectToRoute('game_setup_family');
        }

        /** @var Utilisateur $user */
        $user = $this->getUser();

        // 1) Calculer le budget/bonheur de départ selon la situation
        $presets = [
            'bebe'     => ['budget' => '1200.00', 'bonheur' => 70],
            'ado'      => ['budget' => '1000.00', 'bonheur' => 65],
            'les_deux' => ['budget' => '900.00',  'bonheur' => 60],
        ];
        $base = $presets[$composition] ?? ['budget' => '1000.00', 'bonheur' => 65];

        // 2) Créer la Partie initiale
        $partie = (new Partie())
            ->setUtilisateur($user)
            ->setBudgetCourant($base['budget'])
            ->setBonheurCourant($base['bonheur']);

        // 3) Démarrer la partie via le GameEngine de Karima (4 semaines)
        $this->engine->demarrerPartie($partie, 4);

        // 4) Sauvegarder en base
        $this->em->persist($partie);
        $this->em->flush();

        // 5) Garder l'id de la partie en session si tu veux t'en resservir côté PHP
        $session->set('current_game_id', $partie->getId());

        // 6) Afficher la page "jeu" pour Fouzia (avec l'ID)
        return $this->render('game/play.html.twig', [
            'partie'       => $partie,
            'composition'  => $composition,
            'logement'     => $logement,
            'situationPro' => $situationPro,
        ]);
    }
}
