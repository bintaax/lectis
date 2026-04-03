<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Entité Doctrine pour ligne commande.
#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation ManyToOne -> Commande
    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commandes $commande = null;

    // Relation ManyToOne -> Livre
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null; // important pour garder l'historique

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de commande.
    public function getCommande(): ?Commandes
    {
        return $this->commande;
    }

    // Met à jour la valeur de commande.
    public function setCommande(?Commandes $commande): static
    {
        $this->commande = $commande;
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

    // Retourne la valeur de prix unitaire.
    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    // Met à jour la valeur de prix unitaire.
    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }
}
