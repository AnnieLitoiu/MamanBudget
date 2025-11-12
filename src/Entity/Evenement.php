<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $texte = '';

    //  (week1, week2, ...)
    #[ORM\Column(length: 20)]
    private string $semaine = 'week1';

      // (bebe, ado, deux)
    #[ORM\Column(length: 20)]
    private string $scenario = 'bebe';

    // #[ORM\Column(type: 'text', nullable: true)]
    // private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $consequenceType = null;

    #[ORM\Column(nullable: true)]
    private ?int $semaineApplicable = null;

    #[ORM\Column(length: 20)]
    private string $type = 'REGULIER'; // SURPRISE | REGULIER

    /** @var Collection<int, Option> */
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Option::class, cascade: ['persist', 'remove'])]
    private Collection $options;

    /** @var Collection<int, Semaine> */
    #[ORM\OneToMany(mappedBy: 'evenementCourant', targetEntity: Semaine::class)]
    private Collection $semainesActuelles;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->semainesActuelles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;
        return $this;
    }

    public function getSemaine(): ?string
    {
        return $this->semaine;
    }

    public function setSemaine(?string $semaine): static
    {
        $this->semaine = $semaine;
        return $this;
    }

    public function getScenario(): ?string
    {
        return $this->scenario;
    }

    public function setScenario(?string $scenario): static
    {
        $this->scenario = $scenario;
        return $this;
    }

    public function getSemaineApplicable(): ?int
    {
        return $this->semaineApplicable;
    }

    public function setSemaineApplicable(?int $semaineApplicable): static
    {
        $this->semaineApplicable = $semaineApplicable;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /** @return Collection<int, Option> */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setEvenement($this);
        }
        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            if ($option->getEvenement() === $this) {
                $option->setEvenement(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Semaine> */
    public function getSemainesActuelles(): Collection
    {
        return $this->semainesActuelles;
    }
}
