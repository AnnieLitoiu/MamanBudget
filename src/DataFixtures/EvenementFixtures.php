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
     $jsonData = file_get_contents($jsonPath);
     $data = json_decode($jsonData, true);

     if (!isset($data['weeks'])) {
        throw new \Exception('Le fichier JSON n\'est pas au format attendu');
     }

        foreach ($data['weeks'] as $weekName => $weekData) {
            //  On boucle sur chaque catégorie : bébé / ado / deux
            foreach (['bebe', 'ado', 'deux'] as $type) {
                if (!isset($weekData[$type])) continue;

                foreach ($weekData[$type] as $eventData) {
                    $event = new Evenement();
                    $event->setTexte($eventData['text']);
                    $event->setSemaine($weekName);
                    $event->setScenario($type);
                    $event->setType('REGULIER');
                    $event->setSemaineApplicable(substr ($weekName, -1));

                    foreach ($eventData['choices'] as $choiceData) {
                        $option = new Option();
                        $option->setLibelle($choiceData['text']);
                        $impact = $choiceData['impact'] ?? [];

                        // Map the impact values to the corresponding fields
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
                $manager->persist($event);
            }
        }            // Ajout de l’événement aléatoire (nouveauté)
            if (isset($weekData['evenementAleatoire'])) {
                $aleatoire = $weekData['evenementAleatoire'];

                $event = new Evenement();
                $event->setTexte($aleatoire['text']);
                $event->setSemaine($weekName);
                $event->setScenario('bonus');
                $event->setType('BONUS');
                $event->setSemaineApplicable(substr($weekName, -1));

                $option = new Option();
                $option->setLibelle('Effet aléatoire sur le budget');
                $option->setDeltaBudget((int)($aleatoire['impact']['budget'] ?? 0));
                $option->setDeltaBienEtre(0);
                $option->setEvenement($event);

                $manager->persist($option);
                $event->addOption($option);
                $manager->persist($event);
            }
                        //  On ajoute aussi le bilan de la semaine (s’il existe)
            if (isset($weekData['bilan'])) {
                $bilanData = $weekData['bilan'];

                $event = new Evenement();
                $event->setTexte(" Bilan de la " . ucfirst($weekName));
                $event->setSemaine($weekName);
                $event->setScenario('bilan');
                $event->setType('BILAN');
                $event->setSemaineApplicable(substr($weekName, -1));
        }

                // On crée les options à partir du résumé et du conseil
                if (isset($bilanData['resume'])) {
                    foreach ($bilanData['resume'] as $key => $text) {
                        $option = new Option();
                        $option->setLibelle(ucfirst($key) . " : " . $text);
                        $option->setDeltaBienEtre(0);
                        $option->setEvenement($event);
                        $manager->persist($option);
                        $event->addOption($option);
                    }
                }
                    if (isset($bilanData['conseil'])) {
                    $option = new Option();
                    $option->setLibelle("Conseil : " . $bilanData['conseil']);
                    $option->setDeltaBienEtre(1);
                    $option->setEvenement($event);
                    $manager->persist($option);
                    $event->addOption($option);
                }

                $manager->persist($event);
            }
                //  Ajout du bilan final du mois
                if (isset($data['bilanFinal'])) {
                    foreach ($data['bilanFinal'] as $niveau => $bilan) {
                        $event = new Evenement();
                        $event->setTexte("🌟 Bilan final du mois (" . ucfirst($niveau) . ")");
                        $event->setSemaine('final');
                        $event->setScenario('bilanFinal');
                        $event->setType('BILAN_FINAL');
                        $event->setSemaineApplicable(5);

                        if (isset($bilan['resume'])) {
                            foreach ($bilan['resume'] as $key => $text) {
                                $option = new Option();
                                $option->setLibelle(ucfirst($key) . " : " . $text);
                                $option->setEvenement($event);
                                $option->setDeltaBienEtre(0);
                        $manager->persist($option);
                        $event->addOption($option);
                    }
                }

                if (isset($bilan['conseil'])) {
                    $option = new Option();
                    $option->setLibelle("Conseil : " . $bilan['conseil']);
                    $option->setDeltaBienEtre(1);
                    $option->setEvenement($event);
                    $manager->persist($option);
                    $event->addOption($option);
                }

                if (isset($bilan['statsSymboliques'])) {
                    foreach ($bilan['statsSymboliques'] as $key => $val) {
                        $option = new Option();
                        $option->setLibelle(ucfirst($key) . " : " . ucfirst($val));
                        $option->setEvenement($event);
                        $option->setDeltaBienEtre(0);
                        $manager->persist($option);
                        $event->addOption($option);
                    }
                }

                $manager->persist($event);
            }
        }

        $manager->flush();
    }
}

