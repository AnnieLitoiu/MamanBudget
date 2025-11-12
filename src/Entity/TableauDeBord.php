<?php

namespace App\Entity;

use App\Repository\TableauDeBordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableauDeBordRepository::class)]
class TableauDeBord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'tableauDeBord')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $classement = null;

    #[ORM\Column(nullable: true)]
    private ?int $meilleurScore = null;

    #[ORM\Column]
    private bool $enregistreScore = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getClassement(): ?array
    {
        return $this->classement;
    }

    public function setClassement(?array $classement): static
    {
        $this->classement = $classement;
        return $this;
    }

    public function getMeilleurScore(): ?int
    {
        return $this->meilleurScore;
    }

    public function setMeilleurScore(?int $meilleurScore): static
    {
        $this->meilleurScore = $meilleurScore;
        return $this;
    }

    public function isEnregistreScore(): bool
    {
        return $this->enregistreScore;
    }

    public function setEnregistreScore(bool $enregistreScore): static
    {
        $this->enregistreScore = $enregistreScore;
        return $this;
    }
}
