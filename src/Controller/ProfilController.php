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

        // Valeurs par défaut
        $stats = [
            'games_played'   => 0,
            'best_bien_etre' => 0,
            'best_bonheur'   => 0,
            'best_budget'    => 0,
        ];

        // Si un profil existe, on utilise ce qu'on peut déjà
        if ($profil) {
            // Ici on réutilise "score" comme un score global (placeholder)
            $stats['best_budget'] = $profil->getScore() ?? 0;

            // Le champ "field" pourra être utilisé plus tard (JSON, label, etc.)
            // pour affiner les stats, mais on ne l'exploite pas encore ici.
        }

        return $this->render('profil/index.html.twig', [
            'stats'  => $stats,
            'profil' => $profil,
        ]);
    }
}
