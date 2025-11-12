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
        $jsonPath = __DIR__ . '/evenements.json';
        if (!is_file($jsonPath)) {
            throw new \RuntimeException("Fichier JSON introuvable: {$jsonPath}");
        }

        $jsonData = file_get_contents($jsonPath);
        try {
            $data = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('JSON invalide dans evenements.json: ' . $e->getMessage());
        }

        if (!isset($data['weeks']) || !is_array($data['weeks'])) {
            throw new \RuntimeException("Structure JSON attendue: { \"weeks\": { ... } }");
        }

        foreach ($data['weeks'] as $weekName => $scenarios) {
            if (!is_array($scenarios)) {
                continue;
            }

            // Essaye d’extraire le numéro de semaine (week1 -> 1), sinon null
            $weekNumber = null;
            if (preg_match('/^week(\d+)$/i', (string)$weekName, $m)) {
                $weekNumber = (int)$m[1];
            }

            foreach ($scenarios as $scenarioName => $events) {
                if (!is_array($events)) {
                    continue;
                }

                foreach ($events as $eventData) {
                    // Sécurité sur les clés
                    $texte = $eventData['text'] ?? '';
                    $choices = $eventData['choices'] ?? [];

                    $event = (new Evenement())
                        ->setTexte($texte)
                        ->setSemaine($weekName)                 // ex: week1
                        ->setScenario((string)$scenarioName)    // ex: bebe | ado | deux
                        ->setSemaineApplicable($weekNumber)     // ex: 1
                        ->setType('REGULIER');

                    foreach ($choices as $choiceData) {
                        $libelle = $choiceData['text'] ?? '';
                        $impact  = $choiceData['impact'] ?? [];

                        $option = (new Option())
                            ->setLibelle($libelle);

                        // budget -> string (selon ton entité Option)
                        if (isset($impact['budget'])) {
                            $option->setDeltaBudget((string)$impact['budget']);
                        }

                        // bienEtre + (stress négatif)
                        $deltaBienEtre = (int)($impact['bienEtre'] ?? 0);
                        if (isset($impact['stress'])) {
                            $deltaBienEtre -= (int)$impact['stress'];
                        }
                        // On ne set que si non nul (si ton setter/support accepte null, tu peux enlever ce if)
                        if ($deltaBienEtre !== 0) {
                            $option->setDeltaBienEtre($deltaBienEtre);
                        }

                        // enfants -> bonheur
                        if (isset($impact['enfants'])) {
                            $option->setDeltaBonheur((int)$impact['enfants']);
                        }

                        // Lien bidirectionnel
                        $event->addOption($option);
                        $manager->persist($option);
                    }

                    $manager->persist($event);
                }
            }
        }

        $manager->flush();
    }
}