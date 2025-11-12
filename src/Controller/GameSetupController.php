<?php

namespace App\Controller;

use App\Form\FamilyChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/setup')]
class GameSetupController extends AbstractController
{
    // Page où l'on choisit la composition familiale (Bébé / Ado / Les deux)
    #[Route('/family', name: 'game_setup_family', methods: ['GET','POST'])]
    public function family(Request $request): Response
    {
        // Exige un utilisateur connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Formulaire
        $form = $this->createForm(FamilyChoiceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $composition = $form->get('composition')->getData(); // bebe | ado | les_deux
            $request->getSession()->set('compositionFamiliale', $composition);

            return $this->redirectToRoute('game_new'); // vérifie que cette route existe
        }

        // Affichage
        return $this->render('game_setup/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
