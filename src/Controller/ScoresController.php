<?php
namespace App\Controller;


use App\Service\GameDesignStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ScoresController extends AbstractController
{
    // Page "Tableau des scores"
    #[Route('/scores', name: 'api_scores', methods: ['GET'])]
    public function scores(GameDesignStats $stats): JsonResponse
    {    
        $scores = $stats->calculerScoresMoyens();
        // Pour l’instant on affiche une page vide/placeholder.
        // On pourra y injecter les vrais scores plus tard.
        return $this->json([
            'scores' => $scores,
        ]);
    }
}

