<?php

namespace App\Entity;

use App\Enum\Statut;
use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Utilisateurs $utilisateurs = null;

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

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
         $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    public function setUtilisateurs(?Utilisateurs $utilisateurs): static
    {
        $this->utilisateurs = $utilisateurs;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(Statut $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getPaiement()
    {
        return $this->paiement;
    }

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

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
{
    return $this->createdAt;
}

public function setCreatedAt(\DateTimeImmutable $createdAt): static
{
    $this->createdAt = $createdAt;
    return $this;
}

}
