<?php

namespace App\Service;

/**
 * Service séparé pour analyser le JSON des événements.
 *
 * Idée :
 * - Ici je ne parle pas d'une "Partie" réelle jouée par une utilisatrice.
 * - Je vais juste regarder tous les événements définis dans le jeu (le JSON)
 *   et calculer des moyennes d'impacts par semaine et par catégorie (bébé, ado, deux).
 *
 * Ce service peut servir :
 * - à équilibrer le jeu (voir si une semaine est trop "dure" ou trop "facile"),
 * - à afficher des stats théoriques,
 * - ou plus tard à comparer le vécu de la joueuse avec le "profil moyen".
 */
class GameDesignStats
{
    /**
     * Je suppose qu'on a un service (déjà existant) qui charge le JSON
     * et expose une méthode getWeeks() qui renvoie une structure comme :
     *
     * [
     *   'week1' => [
     *      'bebe' => [ ...événements... ],
     *      'ado'  => [ ... ],
     *      'deux' => [ ... ],
     *   ],
     *   'week2' => [ ... ],
     *   ...
     * ]
     *
     * Ce loader n'est PAS mon travail ici, on me l'injecte juste.
     */
    public function __construct(
        private EvenementLoader $loader, // <-- adapte le nom/classe si besoin
    ) {
    }

    /**
     * Calcule des moyennes d'impacts à partir du JSON complet.
     *
     * - Je boucle sur chaque semaine (week1, week2, week3, week4, ...).
     * - Pour chaque catégorie (bebe / ado / deux), je regarde tous les événements.
     * - Pour chaque événement, je regarde toutes les options/choices.
     * - J'agrège les impacts (budget, bienEtre, stress, enfants, etc.).
     * - Ensuite je fais une moyenne par type d'impact.
     *
     * Résultat attendu :
     *
     * [
     *   'week1' => [
     *      'bebe' => [
     *          'budget'   => -23.45,
     *          'bienEtre' => 1.2,
     *          'stress'   => 0.5,
     *          ...
     *      ],
     *      'ado'  => [...],
     *      'deux' => [...],
     *   ],
     *   'week2' => [
     *      ...
     *   ],
     *   ...
     * ]
     */
    public function calculerScoresMoyens(): array
    {
        // Je récupère toute la structure "weeks" depuis le loader (le JSON).
        $weeks = $this->loader->getWeeks();

        $result = [];

        foreach ($weeks as $weekName => $weekData) {
            // Je sais que mes catégories dans le JSON sont "bebe", "ado", "deux".
            foreach (['bebe', 'ado', 'deux'] as $category) {
                if (!isset($weekData[$category])) {
                    // Si la catégorie n'existe pas dans cette semaine, je passe.
                    continue;
                }

                $impacts = []; // ex: ['budget' => [..valeurs..], 'bienEtre' => [..], ...]

                // Je parcours tous les événements de cette semaine/catégorie.
                foreach ($weekData[$category] as $event) {
                    // Chaque événement a un tableau 'choices'
                    if (!isset($event['choices']) || !is_array($event['choices'])) {
                        continue;
                    }

                    foreach ($event['choices'] as $choice) {
                        if (!isset($choice['impact']) || !is_array($choice['impact'])) {
                            continue;
                        }

                        // Pour chaque impact (ex: 'budget' => -10, 'bienEtre' => 2, ...)
                        foreach ($choice['impact'] as $key => $value) {
                            // Je stocke toutes les valeurs pour pouvoir en faire une moyenne ensuite.
                            $impacts[$key][] = (float) $value;
                        }
                    }
                }

                // Maintenant je calcule la moyenne pour chaque type d'impact.
                $averages = [];

                foreach ($impacts as $key => $values) {
                    if (count($values) === 0) {
                        $averages[$key] = 0;
                        continue;
                    }

                    $averages[$key] = round(
                        array_sum($values) / count($values),
                        2 // deux décimales, c'est suffisant pour un affichage
                    );
                }

                // J'enregistre le résultat pour cette semaine + catégorie.
                $result[$weekName][$category] = $averages;
            }
        }

        return $result;
    }

    /**
     * Petite méthode utilitaire pratique :
     *
     * - Permet de demander "juste" une semaine + une catégorie.
     * - Exemple: getScoresPour('week1', 'bebe').
     */
    public function getScoresPour(string $weekName, string $category): ?array
    {
        $all = $this->calculerScoresMoyens();

        return $all[$weekName][$category] ?? null;
    }
}
