<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_dashboard', methods: ['GET'])]
    public function index(ProfilRepository $profilRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var Utilisateur $user */
        $user = $this->getUser();

        // On essaie de récupérer le profil lié à l'utilisateur connecté
        $profil = $profilRepository->findOneBy(['utilisateur' => $user]);
        
        // Parties de l'utilisateur
        $parties = $user->getParties()->toArray();
        $gamesPlayed = count($parties);

        // Valeurs par défaut
        $bestBudgetFinal = 0.0;
        $bestBonheur     = 0;
        $bestBienEtre    = 0;

        foreach ($parties as $partie) {
            $bestBudgetFinal = max($bestBudgetFinal, (float) $partie->getBudgetCourant());
            $bestBonheur     = max($bestBonheur, $partie->getBonheurCourant());
            $bestBienEtre    = max($bestBienEtre, $partie->getBienEtreInitial());
        }

        // Meilleur score (stocké dans Profil::score pour l’instant)
        $bestScore = $profil?->getScore() ?? 0;

        $stats = [
            'games_played'   => $gamesPlayed,
            'best_bien_etre' => $bestBienEtre,
            'best_bonheur'   => $bestBonheur,
            'best_budget'    => $bestBudgetFinal,
            'best_score'     => $bestScore,
        ];

        // Si un profil existe, on utilise ce qu'on peut déjà
        /* if ($profil) {
            if (method_exists($profil, 'getNombreParties')) {
                    $stats['games_played'] = $profil->getNombreParties();
                }

                if (method_exists($profil, 'getBestBudget')) {
                    $stats['best_budget'] = $profil->getBestBudget();
                }

                if (method_exists($profil, 'getBestBienEtre')) {
                    $stats['best_bien_etre'] = $profil->getBestBienEtre();
                }

                if (method_exists($profil, 'getBestBonheur')) {
                    $stats['best_bonheur'] = $profil->getBestBonheur();
                }

                if (method_exists($profil, 'getBestScore')) {
                    $stats['best_score'] = $profil->getBestScore();
                }
 */
        return $this->render('profil/index.html.twig', [
            'stats'  => $stats,
            'profil' => $profil,
            'user'   => $user,
        ]);
    }
}


