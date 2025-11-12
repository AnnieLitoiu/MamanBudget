<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'profils', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $field = null;

    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: TableauDeBord::class, cascade: ['persist', 'remove'])]
    private Collection $tableauDeBords;
    
     public function __construct()
    {
        $this->tableauDeBords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field): static
    {
        $this->field = $field;
        return $this;
    }
    
    /** @return Collection<int, TableauDeBord> */
    public function getTableauDeBords(): Collection
    {
        return $this->tableauDeBords;
    }

    public function addTableauDeBord(TableauDeBord $tableauDeBord): static
    {
        if (!$this->tableauDeBords->contains($tableauDeBord)) {
            $this->tableauDeBords->add($tableauDeBord);
            $tableauDeBord->setProfil($this);
        }
        return $this;
    }

    public function removeTableauDeBord(TableauDeBord $tableauDeBord): static
    {
        if ($this->tableauDeBords->removeElement($tableauDeBord)) {
            if ($tableauDeBord->getProfil() === $this) {
                $tableauDeBord->setProfil(null);
            }
        }
        return $this;
    }
}
