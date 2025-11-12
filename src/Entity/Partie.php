<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'partie', targetEntity: Semaine::class, cascade: ['persist', 'remove'])]
    private Collection $semaines;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $budgetInitial = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $budgetCourant = '0.00';

    #[ORM\Column]
    private int $bienEtreInitial = 0;

    #[ORM\Column]
    private int $bonheurCourant = 0;

    #[ORM\Column]
    private int $semaineCourante = 1;

    #[ORM\Column]
    private int $nbSemaines = 0;

    #[ORM\Column(length: 20)]
    private string $etat = 'EN_COURS';

    public function __construct()
    {
        $this->semaines = new ArrayCollection();
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

    /** @return Collection<int, Semaine> */
    public function getSemaines(): Collection
    {
        return $this->semaines;
    }

    public function addSemaine(Semaine $semaine): static
    {
        if (!$this->semaines->contains($semaine)) {
            $this->semaines->add($semaine);
            $semaine->setPartie($this);
        }
        return $this;
    }

    public function removeSemaine(Semaine $semaine): static
    {
        if ($this->semaines->removeElement($semaine)) {
            if ($semaine->getPartie() === $this) {
                $semaine->setPartie(null);
            }
        }
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getBudgetInitial(): string
    {
        return $this->budgetInitial;
    }

    public function setBudgetInitial(string $budgetInitial): static
    {
        $this->budgetInitial = $budgetInitial;
        return $this;
    }

    public function getBudgetCourant(): string
    {
        return $this->budgetCourant;
    }

    public function setBudgetCourant(string $budgetCourant): static
    {
        $this->budgetCourant = $budgetCourant;
        return $this;
    }

    public function getBienEtreInitial(): int
    {
        return $this->bienEtreInitial;
    }

    public function setBienEtreInitial(int $bienEtreInitial): static
    {
        $this->bienEtreInitial = $bienEtreInitial;
        return $this;
    }

    public function getBonheurCourant(): int
    {
        return $this->bonheurCourant;
    }

    public function setBonheurCourant(int $bonheurCourant): static
    {
        $this->bonheurCourant = $bonheurCourant;
        return $this;
    }

    public function getSemaineCourante(): int
    {
        return $this->semaineCourante;
    }

    public function setSemaineCourante(int $semaineCourante): static
    {
        $this->semaineCourante = $semaineCourante;
        return $this;
    }

    public function getNbSemaines(): int
    {
        return $this->nbSemaines;
    }

    public function setNbSemaines(int $nbSemaines): static
    {
        $this->nbSemaines = $nbSemaines;
        return $this;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }
}
