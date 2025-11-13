<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Semaine;
use App\Entity\Evenement;
use App\Entity\Option;
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

    /**
     * Démarre la partie APRÈS le choix familial.
     * Appelée après le formulaire de GameSetupController.
     */
    #[Route('/game/start', name: 'game_start', methods: ['GET'])]
    public function start(Request $request): Response
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

        // 3) Démarrer la partie via le GameEngine (4 semaines)
        $this->engine->demarrerPartie($partie, 4);

        // 4) Sauvegarder en base
        $this->em->persist($partie);
        $this->em->flush();

        // 5) Garder l'id de la partie en session si tu veux
        $session->set('current_game_id', $partie->getId());

        // 6) Rediriger vers l'écran de jeu (boucle des semaines)
        return $this->redirectToRoute('game_play', ['id' => $partie->getId()]);
    }

    /**
     * Écran de jeu : affiche la semaine courante + événement + options.
     * GET  = affiche.
     * POST = applique l'option choisie et passe à la semaine suivante.
     */
    #[Route('/game/{id}', name: 'game_play', methods: ['GET', 'POST'])]
    public function play(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var Partie|null $partie */
        $partie = $this->em->getRepository(Partie::class)->find($id);
        if (!$partie) {
            throw $this->createNotFoundException('Partie introuvable.');
        }

        // 🚩 POST : le joueur vient de cliquer sur une option
        if ($request->isMethod('POST') && $partie->getEtat() === 'EN_COURS') {
            $optionId = $request->request->getInt('optionId');

            $semaine = $this->em->getRepository(Semaine::class)
                ->findOneBy([
                    'partie' => $partie,
                    'numero' => $partie->getSemaineCourante(),
                ]);

            if (!$semaine) {
                throw $this->createNotFoundException('Semaine introuvable.');
            }

            /** @var Option|null $option */
            $option    = $this->em->getRepository(Option::class)->find($optionId);
            $evenement = $semaine->getEvenementCourant();

            if (
                !$option ||
                !$evenement ||
                !$evenement->getOptions()->contains($option)
            ) {
                $this->addFlash('error', 'Choix invalide pour cet événement.');
            } else {
                // Logique jeu : appliquer les effets + passer à la semaine suivante
                $this->engine->appliquerOption($partie, $semaine, $option);
                $this->engine->cloturerSemaine($partie, $semaine);
                $this->em->flush();

                // Partie terminée ? → résumé
                if ($partie->getEtat() === 'TERMINE') {
                    return $this->redirectToRoute('game_summary', ['id' => $partie->getId()]);
                }

                // Sinon on recharge la même route pour la semaine suivante
                return $this->redirectToRoute('game_play', ['id' => $partie->getId()]);
            }
        }

        // 🚩 GET : afficher la semaine courante
        $semaine = $this->em->getRepository(Semaine::class)
            ->findOneBy([
                'partie' => $partie,
                'numero' => $partie->getSemaineCourante(),
            ]);

        if (!$semaine) {
            throw $this->createNotFoundException('Semaine introuvable.');
        }

        $evenement = null;

        if ($partie->getEtat() === 'EN_COURS') {
            $evenement = $semaine->getEvenementCourant();

            // Si aucun événement encore assigné à cette semaine, on en pioche un
            if (!$evenement) {
                // Optionnel : récupérer une catégorie dans l'URL (?categorie=bebe/ado/deux)
                $categorie = $request->query->get('categorie');

                $criteria = ['semaineApplicable' => $semaine->getNumero()];
                if ($categorie) {
                    $criteria['consequenceType'] = $categorie;
                }

                $repoEvt   = $this->em->getRepository(Evenement::class);
                $candidats = $repoEvt->findBy($criteria);

                if ($candidats) {
                    $evenement = $candidats[array_rand($candidats)];
                    $semaine->setEvenementCourant($evenement);
                    $this->em->flush();
                }
            }
        }

        return $this->render('game/play.html.twig', [
            'partie'    => $partie,
            'semaine'   => $semaine,
            'evenement' => $evenement,
        ]);
    }

    /**
     * Résumé final de la partie.
     */
    #[Route('/game/{id}/resume', name: 'game_summary', methods: ['GET'])]
    public function summary(int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var Partie|null $partie */
        $partie = $this->em->getRepository(Partie::class)->find($id);
        if (!$partie) {
            throw $this->createNotFoundException('Partie introuvable.');
        }

        $resume = $this->engine->resumeFinal($partie);

        return $this->render('game/summary.html.twig', [
            'partie' => $partie,
            'resume' => $resume,
        ]);
    }
}
