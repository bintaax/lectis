<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Panier;
use App\Entity\Commandes;

// Represente un utilisateur connecte avec son profil, ses commandes et son panier.
#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var Collection<int, Panier>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Panier::class, cascade: ['persist', 'remove'])]
    private Collection $paniers;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Commandes>
     */
    #[ORM\OneToMany(targetEntity: Commandes::class, mappedBy: 'utilisateurs', cascade: ['remove'])]
    private Collection $commandes;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isAdherent = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $adherentAt = null;

    // Initialise les collections liees au compte utilisateur.
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->paniers = new ArrayCollection();
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    // Ajoute un panier au compte et synchronise la relation inverse.
    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setUtilisateur($this);
        }

        return $this;
    }

    // Retire un panier du compte et nettoie la relation inverse si besoin.
    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            if ($panier->getUtilisateur() === $this) {
                $panier->setUtilisateur(null);
            }
        }

        return $this;
    }

    // Renvoie l'identifiant technique de l'utilisateur.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Renvoie l'email utilise pour se connecter.
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Met a jour l'adresse email du compte.
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne l'identifiant visuel du compte pour le systeme de securite.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Renvoie les roles de l'utilisateur en garantissant toujours ROLE_USER.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Met a jour la liste complete des roles attribues au compte.
     *
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Renvoie le mot de passe deja hashé stocke pour l'authentification.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Met a jour le mot de passe hashé du compte.
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Remplace le hash en session par une empreinte CRC32C pour limiter l'exposition du vrai hash.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    // Ne stocke pas d'identifiants temporaires supplementaires sur cette entite.
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // Cette methode reste vide car l'entite ne conserve pas de secret temporaire.
    }

    // Renvoie le nom de famille de l'utilisateur.
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Met a jour le nom de famille du compte.
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    // Renvoie le prenom de l'utilisateur.
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    // Met a jour le prenom du compte.
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, Commandes>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    // Ajoute une commande a l'utilisateur et synchronise la relation inverse.
    public function addCommande(Commandes $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateurs($this);
        }

        return $this;
    }

    // Retire une commande de l'utilisateur et nettoie la relation inverse si besoin.
    public function removeCommande(Commandes $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUtilisateurs() === $this) {
                $commande->setUtilisateurs(null);
            }
        }

        return $this;
    }

    // Indique si l'adresse email a deja ete verifiee.
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    // Met a jour l'etat de verification du compte.
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // Renvoie la date de suppression logique du compte, si elle existe.
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    // Indique si le compte a ete supprime logiquement.
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    // Anonymise le compte et coupe les donnees sensibles lors d'une suppression.
    public function anonymizeAndDeactivate(): void
    {
        $this->deletedAt = new \DateTimeImmutable();

        // Remplace les donnees visibles par des valeurs neutres.
        $this->nom = 'Utilisateur';
        $this->prenom = 'supprimé';

        // Genere un email unique pour conserver la contrainte d'unicite apres anonymisation.
        $idPart = $this->id ?? random_int(100000, 999999);
        $this->email = 'deleted_' . $idPart . '@lectis.local';

        // Invalide le mot de passe pour empecher toute reconnexion.
        $this->password = password_hash(bin2hex(random_bytes(24)), PASSWORD_BCRYPT);

        // Revient au role standard apres suppression du compte.
        $this->roles = ['ROLE_USER'];

        // Repasse l'etat de verification a faux une fois le compte anonymise.
        $this->isVerified = false;
    }

    // Indique si l'utilisateur beneficie de l'abonnement Lectis+.
    public function isAdherent(): bool
    {
        return $this->isAdherent;
    }

    // Renvoie la date de debut d'adhesion Lectis+.
    public function getAdherentAt(): ?\DateTimeImmutable
    {
        return $this->adherentAt;
    }

    // Active l'adhesion Lectis+ et memorise sa date de debut.
    public function becomeAdherent(): void
    {
        $this->isAdherent = true;
        $this->adherentAt = new \DateTimeImmutable();
    }

    // Desactive l'adhesion Lectis+ et efface la date associee.
    public function leaveAdherent(): void
    {
        $this->isAdherent = false;
        $this->adherentAt = null;
    }
}
