<?php

namespace App\Service;

class EventsJsonLoader
{
    private array $data;

    public function __construct(string $jsonPath = null)
    {
        $jsonPath = $jsonPath ?? __DIR__ . '/../../public/data/events.json';

        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("Fichier JSON introuvable : $jsonPath");
        }

        $content  = file_get_contents($jsonPath);
        $decoded  = json_decode($content, true);
        if ($decoded === null) {
            throw new \RuntimeException("JSON invalide dans $jsonPath");
        }

        $this->data = $decoded;
    }

    public function getWeeks(): array
    {
        return $this->data['weeks'] ?? [];
    }
}
