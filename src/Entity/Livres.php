<?php

namespace App\Entity;

use App\Repository\LivresRepository;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Represente un livre du catalogue avec ses informations commerciales et editoriales.
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

    // Renvoie l'identifiant technique du livre.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie le titre affiche dans le catalogue.
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    // Met a jour le titre et regenere le slug utilise dans les URLs.
    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        // Regenerer le slug ici garantit que l'URL suit toujours le titre enregistre.
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($titre);

        return $this;
    }

    // Renvoie le nom de l'auteur du livre.
    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    // Met a jour le nom de l'auteur.
    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    // Renvoie le nom de l'editeur.
    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    // Met a jour l'editeur renseigne pour le livre.
    public function setEditeur(string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    // Renvoie le resume affiche sur la fiche produit.
    public function getResume(): ?string
    {
        return $this->resume;
    }

    // Met a jour le resume editorial du livre.
    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    // Renvoie la date de publication telle qu'elle est stockee dans le catalogue.
    public function getDatePublication(): ?string
    {
        return $this->datePublication;
    }

    // Met a jour la date de publication du livre.
    public function setDatePublication($datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    // Renvoie le nombre de pages affiche sur la fiche du livre.
    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    // Met a jour le nombre de pages du livre.
    public function setNbPages(int $nbPages): static
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    // Renvoie le prix standard du livre.
    public function getPrix(): ?string
    {
        return $this->prix;
    }

    // Met a jour le prix standard du livre.
    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    // Renvoie le chemin ou nom de l'image du livre.
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    // Met a jour l'image associee au livre.
    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    // Indique si le livre est actuellement disponible a la vente.
    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    // Construit le badge HTML affiche sur les fiches selon la disponibilite.
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

    // Met a jour l'etat de disponibilite du livre.
    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    // Renvoie l'age minimum conseille ou autorise.
    public function getAgeAutorise(): ?int
    {
        return $this->ageAutorise;
    }

    // Met a jour l'age minimum associe au livre.
    public function setAgeAutorise(int $ageAutorise): static
    {
        $this->ageAutorise = $ageAutorise;

        return $this;
    }

    // Renvoie le genre litteraire associe au livre.
    public function getGenre(): ?Genres
    {
        return $this->genre;
    }

    // Associe le livre a un genre.
    public function setGenre(?Genres $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    // Indique si le livre doit apparaitre comme best-seller.
    public function isBestSeller(): ?bool
    {
        return $this->isBestSeller;
    }

    // Active ou desactive le marquage best-seller.
    public function setIsBestSeller(?bool $isBestSeller): static
    {
        $this->isBestSeller = $isBestSeller;

        return $this;
    }

    // Renvoie le slug utilise dans l'URL de detail.
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    // Permet de redefinir manuellement le slug si necessaire.
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    // Calcule le prix reduit reserve aux adherents.
    public function getPrixFidelite(): float
    {
        // La reduction appliquee est de 10 % tout en gardant un prix strictement positif.
        return max(0.01, $this->prix - (10 / 100 * $this->prix));
    }
}
