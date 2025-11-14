<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScoresController extends AbstractController
{
    // Page "Tableau des scores"
    #[Route('/scores', name: 'scores_index', methods: ['GET'])]
    public function index(ProfilRepository $profilRepository): Response
    {
        // Récupérer tous les profils avec leurs scores triés par ordre décroissant
        $profils = $profilRepository->createQueryBuilder('p')
            ->leftJoin('p.utilisateur', 'u')
            ->where('p.score IS NOT NULL')
            ->orderBy('p.score', 'DESC')
            ->setMaxResults(50) // Limiter à 50 meilleurs scores
            ->getQuery()
            ->getResult();

        return $this->render('scores/index.html.twig', [
            'profils' => $profils,
        ]);
    }

}
