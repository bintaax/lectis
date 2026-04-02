<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateurs;
use App\Entity\LignePanier;


// Entité Doctrine pour panier.
#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🔥 Le propriétaire du panier
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

    // Logique métier spécifique de l'entité.
    public function __construct()
    {
        $this->lignePaniers = new ArrayCollection();
         $this->createdAt = new \DateTime();
    }



    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de utilisateur.
    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    // Met à jour la valeur de utilisateur.
    public function setUtilisateur(?Utilisateurs $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    // Retourne la valeur de created at.
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    // Met à jour la valeur de created at.
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

    // Logique métier spécifique de l'entité.
    public function addLignePanier(LignePanier $lignePanier): static
    {
        if (!$this->lignePaniers->contains($lignePanier)) {
            $this->lignePaniers->add($lignePanier);
            $lignePanier->setPanier($this);
        }
        return $this;
    }

    // Logique métier spécifique de l'entité.
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
