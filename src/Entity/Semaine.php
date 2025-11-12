<?php

namespace App\Entity;

use App\Repository\SemaineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SemaineRepository::class)]
class Semaine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'semaines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partie $partie = null;

    #[ORM\ManyToOne(inversedBy: 'semainesActuelles')]
    private ?Evenement $evenementCourant = null;

    #[ORM\Column]
    private int $numero = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $budgetRestant = '0.00';

    #[ORM\Column]
    private int $bienEtre = 0;

    #[ORM\Column]
    private int $bonheurEnfants = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): static
    {
        $this->partie = $partie;
        return $this;
    }

    public function getEvenementCourant(): ?Evenement
    {
        return $this->evenementCourant;
    }

    public function setEvenementCourant(?Evenement $evenementCourant): static
    {
        $this->evenementCourant = $evenementCourant;
        return $this;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getBudgetRestant(): string
    {
        return $this->budgetRestant;
    }

    public function setBudgetRestant(string $budgetRestant): static
    {
        $this->budgetRestant = $budgetRestant;
        return $this;
    }

    public function getBienEtre(): int
    {
        return $this->bienEtre;
    }

    public function setBienEtre(int $bienEtre): static
    {
        $this->bienEtre = $bienEtre;
        return $this;
    }

    public function getBonheurEnfants(): int
    {
        return $this->bonheurEnfants;
    }

    public function setBonheurEnfants(int $bonheurEnfants): static
    {
        $this->bonheurEnfants = $bonheurEnfants;
        return $this;
    }
}
