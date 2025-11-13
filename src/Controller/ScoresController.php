<?php
namespace App\Controller;

use App\Service\GameEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ScoresController extends AbstractController
{
    // Page "Tableau des scores"
    #[Route('/scores', name: 'app_scores')]
    public function scores(GameEngine $engine): Response
    {    
        $scores = $engine ->calculerScoresMoyens();
        // Pour l’instant on affiche une page vide/placeholder.
        // On pourra y injecter les vrais scores plus tard.
        return $this->render('scores/index.html.twig', [
            'scores' => $scores,
        ]);
    }
}

