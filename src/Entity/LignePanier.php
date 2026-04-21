<?php

namespace App\Entity;

use App\Repository\LignePanierRepository;
use Doctrine\ORM\Mapping as ORM;

// Represente une ligne d'article presente dans le panier courant.
#[ORM\Entity(repositoryClass: LignePanierRepository::class)]
class LignePanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Pointe vers le panier auquel cette ligne appartient.
    #[ORM\ManyToOne(inversedBy: 'lignePaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    // Pointe vers le livre ajoute dans le panier.
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    #[ORM\Column]
    private ?int $quantite = 1;

    // Renvoie l'identifiant technique de la ligne.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie le panier proprietaire de cette ligne.
    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    // Rattache cette ligne a un panier.
    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;
        return $this;
    }

    // Renvoie le livre associe a la ligne du panier.
    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    // Associe un livre a la ligne du panier.
    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;
        return $this;
    }

    // Renvoie la quantite choisie pour ce livre.
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    // Met a jour la quantite stockee dans le panier.
    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }
}
