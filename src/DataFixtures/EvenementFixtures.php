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
            // Rien à charger, on laisse passer les autres fixtures.
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
            if (!is_array($events)) {
                continue;
            }
     foreach ($events as $eventData) {
        $event = new Evenement();
        $event->setTexte($eventData['text']);
        $event->setSemaine($situation);
        $event->setScenario($eventData['scenario']?? null);
        $event->setSemaineApplicable($eventData['weekNumber']?? null);
        $event->setType('REGULIER');
        foreach ($eventData['choices'] as $choiceData) {
            $option = new Option();
            $option->setLibelle($choiceData['text']);
            
            // Map the impact values to the corresponding fields
            $impact = $choiceData['impact'] ?? [];
            if (isset($impact['budget'])) {
                $option->setDeltaBudget((string)$impact['budget']);
            }
            if (isset($impact['bienEtre'])) {
                $option->setDeltaBienEtre((int)$impact['bienEtre']);
            }
            if (isset($impact['stress'])) {
                // Assuming stress affects bienEtre negatively
                $option->setDeltaBienEtre($option->getDeltaBienEtre() - (int)$impact['stress']);
            }
            if (isset($impact['enfants'])) {
                // Assuming enfants affects bonheur
                $option->setDeltaBonheur((int)$impact['enfants']);
            }
            
            $option->setEvenement($event);

            $manager->persist($option);
            $event->addOption($option);
        }

        $manager->flush();
    }
    }
}
}
