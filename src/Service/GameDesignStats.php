<?php

namespace App\Service;

use App\Service\EventsJsonLoader;

class GameDesignStats
{
    public function __construct(
        private EventsJsonLoader $loader
    ) {}

    public function calculerScoresMoyens(): array
    {
        $weeks = $this->loader->getWeeks();
        $result = [];

        foreach ($weeks as $weekName => $weekData) {
            foreach (['bebe', 'ado', 'deux'] as $category) {
                if (!isset($weekData[$category])) continue;

                $impacts = [];

                foreach ($weekData[$category] as $event) {
                    if (!isset($event['choices'])) continue;

                    foreach ($event['choices'] as $choice) {
                        if (!isset($choice['impact'])) continue;

                        foreach ($choice['impact'] as $key => $value) {
                            $impacts[$key][] = (float) $value;
                        }
                    }
                }

                $averages = [];
                foreach ($impacts as $key => $values) {
                    $averages[$key] = count($values)
                        ? round(array_sum($values) / count($values), 2)
                        : 0;
                }

                $result[$weekName][$category] = $averages;
            }
        }

        return $result;
    }
}
