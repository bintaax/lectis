<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateurs;
use App\Entity\LignePanier;

// Represente le panier actif d'un utilisateur et ses lignes associees.
#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Identifie l'utilisateur proprietaire du panier.
    #[ORM\ManyToOne(inversedBy: 'paniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $utilisateur = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    /**
     * @var Collection<int, LignePanier>
     */
    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: LignePanier::class, cascade: ['persist', 'remove'])]
    private Collection $lignePaniers;

    // Initialise la collection des lignes et la date de creation du panier.
    public function __construct()
    {
        $this->lignePaniers = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Renvoie l'identifiant technique du panier.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie l'utilisateur proprietaire du panier.
    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    // Associe ce panier a un utilisateur.
    public function setUtilisateur(?Utilisateurs $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    // Renvoie la date de creation du panier.
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    // Met a jour la date de creation du panier.
    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, LignePanier>
     */
    public function getLignePaniers(): Collection
    {
        return $this->lignePaniers;
    }

    // Ajoute une ligne au panier et synchronise la relation inverse.
    public function addLignePanier(LignePanier $lignePanier): static
    {
        if (!$this->lignePaniers->contains($lignePanier)) {
            $this->lignePaniers->add($lignePanier);
            $lignePanier->setPanier($this);
        }

        return $this;
    }

    // Retire une ligne du panier et nettoie la relation inverse si besoin.
    public function removeLignePanier(LignePanier $lignePanier): static
    {
        if ($this->lignePaniers->removeElement($lignePanier)) {
            if ($lignePanier->getPanier() === $this) {
                $lignePanier->setPanier(null);
            }
        }

        return $this;
    }
}
