<?php

namespace App\Entity;

use App\Repository\LivresRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

// Entité Doctrine pour livres.
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

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $datePublication = null;

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

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de titre.
    public function getTitre(): ?string
    {
        return $this->titre;
    }

   
    // Met à jour la valeur de titre.
    public function setTitre(?string $titre): static
{
    $this->titre = $titre;

    // Génère automatiquement le slug
    $slugify = new Slugify();
    $this->slug = $slugify->slugify($titre);

    return $this;
}

    // Retourne la valeur de auteur.
    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    // Met à jour la valeur de auteur.
    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    // Retourne la valeur de editeur.
    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    // Met à jour la valeur de editeur.
    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    // Retourne la valeur de resume.
    public function getResume(): ?string
    {
        return $this->resume;
    }

    // Met à jour la valeur de resume.
    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    // Retourne la valeur de date publication.
    public function getDatePublication(): ?string
    {
        return $this->datePublication;
    }

    // Met à jour la valeur de date publication.
    public function setDatePublication( $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    // Retourne la valeur de nb pages.
    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    // Met à jour la valeur de nb pages.
    public function setNbPages(int $nbPages): static
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    // Retourne la valeur de prix.
    public function getPrix(): ?string
    {
        return $this->prix;
    }

    // Met à jour la valeur de prix.
    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    // Retourne la valeur de photo.
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    // Met à jour la valeur de photo.
    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    // Indique si disponibilite.
    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    // Retourne la valeur de disponibilite badge.
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


    // Met à jour la valeur de disponibilite.
    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    // Retourne la valeur de age autorise.
    public function getAgeAutorise(): ?int
    {
        return $this->ageAutorise;
    }

    // Met à jour la valeur de age autorise.
    public function setAgeAutorise(int $ageAutorise): static
    {
        $this->ageAutorise = $ageAutorise;

        return $this;
    }

    // Retourne la valeur de genre.
    public function getGenre(): ?Genres
    {
        return $this->genre;
    }

    // Met à jour la valeur de genre.
    public function setGenre(?Genres $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    // Indique si best seller.
    public function isBestSeller(): ?bool
    {
        return $this->isBestSeller;
    }

    // Met à jour la valeur de is best seller.
    public function setIsBestSeller(?bool $isBestSeller): static
    {
        $this->isBestSeller = $isBestSeller;

        return $this;
    }

    // Retourne la valeur de slug.
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    // Met à jour la valeur de slug.
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    // Retourne la valeur de prix fidelite.
    public function getPrixFidelite(): float
{
    // On retourne le prix actuel moins 1 euro
    // On s'assure que le prix ne tombe pas en dessous de 0.01€
    return max(0.01, $this->prix - (10/100 * $this->prix));
}
}
