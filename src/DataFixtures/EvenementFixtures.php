<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Option;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class EvenementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1) Chemin attendu : src/DataFixtures/evenements.json
        $path = __DIR__ . '/evenements.json';

        // 2) Si le fichier n'existe pas => on NE plante PAS, on sort.
        if (!is_file($path)) {
            return;
        }

        // 3) Lire et décoder en sécurité
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            // JSON invalide => on sort sans bloquer
            return;
        }

        // 4) Parcours des données
        foreach ($data as $situation => $events) {
            // On s’assure que $events est bien un tableau
            if (!is_array($events)) {
                continue;
            }

            foreach ($events as $eventData) {
                // On s’assure que $eventData est bien un tableau
                if (!is_array($eventData)) {
                    continue;
                }

                $event = new Evenement();

                // Texte de l’événement
                $event->setTexte($eventData['text'] ?? '');
                // Ici tu mets "semaine" = clé de niveau 1 (ex : "Semaine 1")
                $event->setSemaine($situation);
                $event->setScenario($eventData['scenario'] ?? '');
                $event->setSemaineApplicable($eventData['weekNumber'] ?? null);
                $event->setType('REGULIER');

                // Récupération des choix
                $choices = $eventData['choices'] ?? [];
                if (!is_array($choices)) {
                    $choices = [];
                }

                foreach ($choices as $choiceData) {
                    // Pareil, on vérifie que c’est un tableau
                    if (!is_array($choiceData)) {
                        continue;
                    }

                    $option = new Option();
                    $option->setLibelle($choiceData['text'] ?? '');

                    // Impact
                    $impact = $choiceData['impact'] ?? [];
                    if (!is_array($impact)) {
                        $impact = [];
                    }

                    // Budget (string en DB)
                    if (array_key_exists('budget', $impact)) {
                        $option->setDeltaBudget((string) $impact['budget']);
                    }

                    // Bien-être + stress → on combine dans un même delta
                    $deltaBienEtre = 0;
                    if (array_key_exists('bienEtre', $impact)) {
                        $deltaBienEtre += (int) $impact['bienEtre'];
                    }
                    if (array_key_exists('stress', $impact)) {
                        // On considère que le stress diminue le bien-être
                        $deltaBienEtre -= (int) $impact['stress'];
                    }
                    $option->setDeltaBienEtre($deltaBienEtre);

                    // Bonheur des enfants
                    if (array_key_exists('enfants', $impact)) {
                        $option->setDeltaBonheur((int) $impact['enfants']);
                    }

                    $option->setEvenement($event);
                    $event->addOption($option);

                    $manager->persist($option);
                }

                $manager->persist($event);
            }
        }

        // On flush une seule fois à la fin
        $manager->flush();
    }

}