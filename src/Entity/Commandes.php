<?php

namespace App\Entity;

use App\Enum\Statut;
use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Entité Doctrine pour commandes.
#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // FK existante dans ta DB : commandes.utilisateurs_id -> utilisateurs.id
    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'utilisateurs_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateurs $utilisateurs = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $numeroCommande = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(enumType: Statut::class)]
    private ?Statut $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(length: 50)]
    private ?string $paiement = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'])]
    private Collection $ligneCommandes;

    // Logique métier spécifique de l'entité.
    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();

        // garde ton format actuel
        $this->numeroCommande = 'CMD-' . strtoupper(bin2hex(random_bytes(4)));
    }

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de utilisateurs.
    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }


    // mais en pratique, vu nullable:false, évite de passer null.
    public function setUtilisateurs(?Utilisateurs $utilisateurs): static
    {
        $this->utilisateurs = $utilisateurs;
        return $this;
    }

    // Retourne la valeur de numero commande.
    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    // Met à jour la valeur de numero commande.
    public function setNumeroCommande(string $numeroCommande): static
    {
        $this->numeroCommande = $numeroCommande;
        return $this;
    }

    // Retourne la valeur de created at.
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Met à jour la valeur de created at.
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Retourne la valeur de total.
    public function getTotal(): ?float
    {
        return $this->total;
    }

    // Met à jour la valeur de total.
    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    // Retourne la valeur de statut.
    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    // Met à jour la valeur de statut.
    public function setStatut(Statut $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    // Retourne la valeur de adresse livraison.
    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    // Met à jour la valeur de adresse livraison.
    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    // Retourne la valeur de paiement.
    public function getPaiement(): ?string
    {
        return $this->paiement;
    }

    // Met à jour la valeur de paiement.
    public function setPaiement(string $paiement): static
    {
        $this->paiement = $paiement;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    // Logique métier spécifique de l'entité.
    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    // Logique métier spécifique de l'entité.
    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function isAnnulee(): bool
    {
        return $this->statut === Statut::ANNULEE;
    }

    public function getElapsedDays(): int
    {
        $createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $now = new \DateTimeImmutable();

        return (int) floor(($now->getTimestamp() - $createdAt->getTimestamp()) / 86400);
    }

    public function isAnnulable(): bool
    {
        return !$this->isAnnulee() && $this->getElapsedDays() < 3;
    }

    // Retourne la valeur de statut simule.
    public function getStatutSimule(): Statut
    {
        if ($this->isAnnulee()) {
            return Statut::ANNULEE;
        }

        $diffDays = $this->getElapsedDays();

        return match (true) {
            $diffDays >= 7 => Statut::LIVREE,
            $diffDays >= 3 => Statut::LIVRAISON,
            $diffDays >= 1 => Statut::PREPARATION,
            default => Statut::PASSEE,
        };
    }


    
}
