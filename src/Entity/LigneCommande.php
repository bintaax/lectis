<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

// Represente une ligne d'article rattachee a une commande validee.
#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Pointe vers la commande qui contient cette ligne.
    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commandes $commande = null;

    // Conserve le livre commande a cette ligne.
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livres $livre = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null; // Conserve le prix paye au moment de la commande.

    // Renvoie l'identifiant technique de la ligne.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie la commande a laquelle cette ligne appartient.
    public function getCommande(): ?Commandes
    {
        return $this->commande;
    }

    // Rattache cette ligne a une commande.
    public function setCommande(?Commandes $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    // Renvoie le livre memorise dans cette ligne de commande.
    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    // Associe un livre a cette ligne de commande.
    public function setLivre(?Livres $livre): static
    {
        $this->livre = $livre;
        return $this;
    }

    // Renvoie la quantite commandee pour ce livre.
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    // Met a jour la quantite commandee.
    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    // Renvoie le prix unitaire enregistre pour conserver l'historique.
    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    // Met a jour le prix unitaire memorise sur la ligne.
    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }
}
