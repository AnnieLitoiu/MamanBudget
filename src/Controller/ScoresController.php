<?php

namespace App\Controller;

use App\Service\GameDesignStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScoresController extends AbstractController
{
    // Page "Tableau des scores"
    #[Route('/scores', name: 'scores_index', methods: ['GET'])]
    public function index(GameDesignStats $stats): Response
    {
        // On calcule les scores (tu pourras les afficher plus tard dans le Twig)
        $scores = $stats->calculerScoresMoyens();

        return $this->render('scores/index.html.twig', [
            'scores' => $scores,
        ]);
    }
}
