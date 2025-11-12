<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenement $evenement = null;

    #[ORM\Column(length: 255)]
    private string $libelle = '';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $deltaBudget = '0.00';

    #[ORM\Column]
    private int $deltaBienEtre = 0;

    #[ORM\Column]
    private int $deltaBonheur = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getDeltaBudget(): string
    {
        return $this->deltaBudget;
    }

    public function setDeltaBudget(string $deltaBudget): static
    {
        $this->deltaBudget = $deltaBudget;
        return $this;
    }

    public function getDeltaBienEtre(): int
    {
        return $this->deltaBienEtre;
    }

    public function setDeltaBienEtre(int $deltaBienEtre): static
    {
        $this->deltaBienEtre = $deltaBienEtre;
        return $this;
    }

    public function getDeltaBonheur(): int
    {
        return $this->deltaBonheur;
    }

    public function setDeltaBonheur(int $deltaBonheur): static
    {
        $this->deltaBonheur = $deltaBonheur;
        return $this;
    }
}
