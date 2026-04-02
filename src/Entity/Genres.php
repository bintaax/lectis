<?php

namespace App\Entity;

use App\Repository\GenresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Entité Doctrine pour genres.
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

    // Logique métier spécifique de l'entité.
    public function __construct()
    {
        $this->livres = new ArrayCollection();
    }

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de nom.
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Met à jour la valeur de nom.
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    // Logique métier spécifique de l'entité.
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

    // Logique métier spécifique de l'entité.
    public function addLivre(Livres $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
            $livre->setGenre($this);
        }

        return $this;
    }

    // Logique métier spécifique de l'entité.
    public function removeLivre(Livres $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getGenre() === $this) {
                $livre->setGenre(null);
            }
        }

        return $this;
    }
}
