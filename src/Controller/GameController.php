<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game/new', name: 'game_new', methods: ['GET'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $composition = $request->getSession()->get('compositionFamiliale'); // 'bebe' | 'ado' | 'les_deux'
        if (!$composition) {
            $this->addFlash('warning', 'Choisis d’abord ta situation familiale.');
            return $this->redirectToRoute('game_setup_family');
        }

        // Init selon la composition (exemple simple)
        $presets = [
            'bebe'     => ['budget' => 1200, 'bien_etre' => 65, 'bonheur' => 70],
            'ado'      => ['budget' => 1000, 'bien_etre' => 70, 'bonheur' => 65],
            'les_deux' => ['budget' => 900,  'bien_etre' => 60, 'bonheur' => 60],
        ];
        $base = $presets[$composition] ?? ['budget' => 1000, 'bien_etre' => 65, 'bonheur' => 65];

        $request->getSession()->set('game_state', [
            'week'        => 1,
            'budget'      => $base['budget'],
            'bien_etre'   => $base['bien_etre'],
            'bonheur'     => $base['bonheur'],
            'composition' => $composition,
        ]);

        return $this->redirectToRoute('game_week1');
    }
}
