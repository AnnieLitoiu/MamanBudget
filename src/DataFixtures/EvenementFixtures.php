<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Option;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;


class EvenementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
     $jsonPath = __DIR__ . '/evenements.json';
     $jsonData = file_get_contents($jsonPath);
    //  $serializer = new Serializer([ new JsonSerializableNormalizer()], [new JsonEncoder()]);
     $data = json_decode($jsonData, true);

     foreach ($data as $situation => $events) {
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
        $manager->persist($event);
     }

        $manager->flush();
    }
}
}
