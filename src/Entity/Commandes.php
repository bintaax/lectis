<?php

namespace App\Entity;

use App\Enum\Statut;
use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Represente une commande validee avec son client, ses lignes et son statut.
#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Pointe vers l'utilisateur qui a passe la commande.
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

    // Initialise la collection des lignes, la date et un numero de commande unique.
    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->numeroCommande = 'CMD-' . strtoupper(bin2hex(random_bytes(4)));
    }

    // Renvoie l'identifiant technique de la commande.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie l'utilisateur associe a la commande.
    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    // Associe la commande a un utilisateur.
    public function setUtilisateurs(?Utilisateurs $utilisateurs): static
    {
        $this->utilisateurs = $utilisateurs;
        return $this;
    }

    // Renvoie le numero unique affiche au client.
    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    // Permet de redefinir manuellement le numero de commande.
    public function setNumeroCommande(string $numeroCommande): static
    {
        $this->numeroCommande = $numeroCommande;
        return $this;
    }

    // Renvoie la date de creation de la commande.
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Met a jour la date de creation de la commande.
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Renvoie le total final de la commande.
    public function getTotal(): ?float
    {
        return $this->total;
    }

    // Met a jour le total final enregistre pour la commande.
    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    // Renvoie le statut courant de la commande.
    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    // Met a jour le statut enregistre en base.
    public function setStatut(Statut $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    // Renvoie l'adresse de livraison de la commande.
    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    // Met a jour l'adresse de livraison stockee sur la commande.
    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    // Renvoie le mode de paiement choisi.
    public function getPaiement(): ?string
    {
        return $this->paiement;
    }

    // Met a jour le mode de paiement memorise sur la commande.
    public function setPaiement(string $paiement): static
    {
        $this->paiement = $paiement;
        return $this;
    }

    // Fournit un libelle client concis pour l'administration.
    public function getClientLabel(): string
    {
        $utilisateur = $this->utilisateurs;

        if (!$utilisateur) {
            return 'Client indisponible';
        }

        $nom = trim(($utilisateur->getPrenom() ?? '') . ' ' . ($utilisateur->getNom() ?? ''));

        return $nom !== '' ? $nom : ($utilisateur->getEmail() ?? 'Client indisponible');
    }

    // Expose le statut affiche dans l'admin a partir de la simulation metier.
    public function getStatutAdminLabel(): string
    {
        return $this->getStatutSimule()->label();
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    // Ajoute une ligne a la commande et synchronise la relation inverse.
    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    // Retire une ligne de la commande et nettoie la relation inverse si besoin.
    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    // Indique si la commande a deja ete annulee explicitement.
    public function isAnnulee(): bool
    {
        return $this->statut === Statut::ANNULEE;
    }

    // Calcule le nombre de jours ecoules depuis la creation de la commande.
    public function getElapsedDays(): int
    {
        $createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $now = new \DateTimeImmutable();

        return (int) floor(($now->getTimestamp() - $createdAt->getTimestamp()) / 86400);
    }

    // Calcule le nombre d'heures ecoulees depuis la creation de la commande.
    public function getElapsedHours(): int
    {
        $createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $now = new \DateTimeImmutable();

        return (int) floor(($now->getTimestamp() - $createdAt->getTimestamp()) / 3600);
    }

    // Determine si la commande peut encore etre annulee selon la regle metier.
    public function isAnnulable(): bool
    {
        return !$this->isAnnulee() && $this->getElapsedHours() < 24;
    }

    // Simule un statut d'avancement a partir du temps ecoule pour l'affichage.
    public function getStatutSimule(): Statut
    {
        if ($this->isAnnulee()) {
            return Statut::ANNULEE;
        }

        $diffHours = $this->getElapsedHours();

        return match (true) {
            $diffHours >= 48 => Statut::LIVREE,
            $diffHours >= 24 => Statut::LIVRAISON,
            $diffHours >= 6 => Statut::PREPARATION,
            default => Statut::PASSEE,
        };
    }
}
