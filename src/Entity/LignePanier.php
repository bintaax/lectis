<?php

namespace App\Entity;

use App\Repository\LignePanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Entité Doctrine pour ligne panier.
#[ORM\Entity(repositoryClass: LignePanierRepository::class)]
class LignePanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation : ManyToOne → Panier
    #[ORM\ManyToOne(inversedBy: 'lignePaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    // Relation : ManyToOne → Livres
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    #[ORM\Column]
    private ?int $quantite = 1;

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de panier.
    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    // Met à jour la valeur de panier.
    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;
        return $this;
    }

    // Retourne la valeur de livre.
    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    // Met à jour la valeur de livre.
    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;
        return $this;
    }

    // Retourne la valeur de quantite.
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    // Met à jour la valeur de quantite.
    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }
}
