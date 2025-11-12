<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Profil::class, cascade: ['persist', 'remove'])]
    private Collection $profils;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Partie::class, cascade: ['persist', 'remove'])]
    private Collection $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
        $this->profils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    /** @return Collection<int, Partie> */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addPartie(Partie $partie): static
    {
        if (!$this->parties->contains($partie)) {
            $this->parties->add($partie);
            $partie->setUtilisateur($this);
        }
        return $this;
    }

    public function removePartie(Partie $partie): static
    {
        if ($this->parties->removeElement($partie)) {
            if ($partie->getUtilisateur() === $this) {
                $partie->setUtilisateur(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Profil> */
    public function getProfils(): Collection
    {
        return $this->profils;
    }

    public function addProfil(Profil $profil): static
    {
        if (!$this->profils->contains($profil)) {
            $this->profils->add($profil);
            $profil->setUtilisateur($this);
        }
        return $this;
    }

    public function removeProfil(Profil $profil): static
    {
        if ($this->profils->removeElement($profil)) {
            if ($profil->getUtilisateur() === $this) {
                $profil->setUtilisateur(null);
            }
        }
        return $this;
    }
    /* ===================== SECURITY ===================== */

    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // si tu avais un plainPassword temporaire tu l’effacerais ici
    }
}
