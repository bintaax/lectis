<?php

namespace App\Entity;

use App\Repository\GenresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Represente un genre litteraire rattache a plusieurs livres.
#[ORM\Entity(repositoryClass: GenresRepository::class)]
class Genres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Livres>
     */
    #[ORM\OneToMany(targetEntity: Livres::class, mappedBy: 'genre')]
    private Collection $livres;

    // Initialise la collection qui contient les livres du genre.
    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    // Renvoie l'identifiant technique du genre.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie le nom affiche pour ce genre.
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Met a jour le nom du genre.
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    // Retourne le nom du genre lorsqu'on affiche directement l'objet.
    public function __toString(): string
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, Livres>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    // Ajoute un livre au genre et synchronise la relation cote livre.
    public function addLivre(Livres $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setGenre($this);
        }

        return $this;
    }

    // Retire un livre du genre et nettoie la relation inverse si besoin.
    public function removeLivre(Livres $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            if ($livre->getGenre() === $this) {
                $livre->setGenre(null);
            }
        }

        return $this;
    }
}
