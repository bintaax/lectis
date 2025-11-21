<?php

namespace App\Entity;

use App\Repository\LivresRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivresRepository::class)]
class Livres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $auteur = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $resume = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $datePublication = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $nombrePages = null;

    #[ORM\Column]
    private ?bool $isBestSeller = null;

    #[ORM\Column(length: 255)]
    private ?string $editeur = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $ageAutorise = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genres $genreId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTime $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(int $nombrePages): static
    {
        $this->nombrePages = $nombrePages;

        return $this;
    }

    public function isBestSeller(): ?bool
    {
        return $this->isBestSeller;
    }

    public function setIsBestSeller(bool $isBestSeller): static
    {
        $this->isBestSeller = $isBestSeller;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getAgeAutorise(): ?int
    {
        return $this->ageAutorise;
    }

    public function setAgeAutorise(?int $ageAutorise): static
    {
        $this->ageAutorise = $ageAutorise;

        return $this;
    }

    public function getGenreId(): ?Genres
    {
        return $this->genreId;
    }

    public function setGenreId(?Genres $genreId): static
    {
        $this->genreId = $genreId;

        return $this;
    }
}
