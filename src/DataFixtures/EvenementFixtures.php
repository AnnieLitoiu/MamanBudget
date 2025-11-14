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
        $jsonFile = __DIR__ . '/../../public/data/events.json';

        if (!file_exists($jsonFile)) {
            return;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        if (!$data || !isset($data['weeks'])) {
            return;
        }

        foreach ($data['weeks'] as $weekKey => $scenarios) {
            foreach ($scenarios as $scenario => $events) {
                // Ignorer les clés qui ne sont pas des scénarios (evenementAleatoire, bilan, etc.)
                if (!in_array($scenario, ['bebe', 'ado', 'deux']) || !is_array($events)) {
                    continue;
                }

                foreach ($events as $eventData) {
                    $evenement = new Evenement();
                    $evenement->setTexte($eventData['text']);
                    $evenement->setSemaine($weekKey);
                    $evenement->setScenario($scenario);
                    $evenement->setType('REGULIER');

                    $manager->persist($evenement);

                    // Ajouter les options
                    if (isset($eventData['choices'])) {
                        foreach ($eventData['choices'] as $choiceData) {
                            $option = new Option();
                            $option->setLibelle($choiceData['text']);
                            $option->setEvenement($evenement);

                            // Convertir les impacts
                            if (isset($choiceData['impact'])) {
                                $impact = $choiceData['impact'];

                                // Budget
                                if (isset($impact['budget'])) {
                                    $option->setDeltaBudget((string) $impact['budget']);
                                }

                                // Bien-être (peut être bienEtre, stress, etc.)
                                $bienEtre = 0;
                                if (isset($impact['bienEtre'])) {
                                    $bienEtre += $impact['bienEtre'];
                                }
                                if (isset($impact['stress'])) {
                                    $bienEtre -= $impact['stress'];
                                }
                                $option->setDeltaBienEtre($bienEtre);

                                // Bonheur (peut être enfants, bonheur, etc.)
                                $bonheur = 0;
                                if (isset($impact['enfants'])) {
                                    $bonheur += $impact['enfants'];
                                }
                                if (isset($impact['bonheur'])) {
                                    $bonheur += $impact['bonheur'];
                                }
                                $option->setDeltaBonheur($bonheur);
                            }

                            $manager->persist($option);
                        }
                    }
                }
            }
        }

        $manager->flush();
    }
}
