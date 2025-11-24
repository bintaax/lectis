<?php

namespace App\Entity;

use App\Repository\GenresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    #[ORM\OneToMany(targetEntity: Livres::class, mappedBy: 'genreId')]
    private Collection $livres;

    /**
     * @var Collection<int, Livres>
     */
    #[ORM\OneToMany(targetEntity: Livres::class, mappedBy: 'genre')]
    private Collection $genreId;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
        $this->genreId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Livres>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livres $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
           
        }

        return $this;
    }



    /**
     * @return Collection<int, Livres>
     */
    public function getGenreId(): Collection
    {
        return $this->genreId;
    }

    public function addGenreId(Livres $genreId): static
    {
        if (!$this->genreId->contains($genreId)) {
            $this->genreId->add($genreId);
            $genreId->setGenre($this);
        }

        return $this;
    }

    public function removeGenreId(Livres $genreId): static
    {
        if ($this->genreId->removeElement($genreId)) {
            // set the owning side to null (unless already changed)
            if ($genreId->getGenre() === $this) {
                $genreId->setGenre(null);
            }
        }

        return $this;
    }
}
