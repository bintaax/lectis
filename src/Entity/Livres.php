<?php

namespace App\Entity;

use App\Repository\LivresRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

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

    #[ORM\Column(length: 255)]
    private ?string $editeur = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $resume = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datePublication = null;

    #[ORM\Column]
    private ?int $nbPages = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;

    #[ORM\Column]
    private ?int $ageAutorise = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    private ?Genres $genre = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBestSeller = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

   
    public function setTitre(?string $titre): static
{
    $this->titre = $titre;

    // Génère automatiquement le slug
    $slugify = new Slugify();
    $this->slug = $slugify->slugify($titre);

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

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

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

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeImmutable $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(int $nbPages): static
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function getDisponibiliteBadge(): string
{
    if ($this->disponibilite) {
        return '<p class="text-green-500 flex items-center gap-1">
                    <i class="fa-solid fa-check text-green-500"></i> Disponible
                </p>';
    }

    return '<p class="text-red-500 flex items-center gap-1">
                <i class="fa-solid fa-xmark text-red-500"></i> Indisponible
            </p>';
}


    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getAgeAutorise(): ?int
    {
        return $this->ageAutorise;
    }

    public function setAgeAutorise(int $ageAutorise): static
    {
        $this->ageAutorise = $ageAutorise;

        return $this;
    }

    public function getGenre(): ?Genres
    {
        return $this->genre;
    }

    public function setGenre(?Genres $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function isBestSeller(): ?bool
    {
        return $this->isBestSeller;
    }

    public function setIsBestSeller(?bool $isBestSeller): static
    {
        $this->isBestSeller = $isBestSeller;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
