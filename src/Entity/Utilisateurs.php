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

// Entité Doctrine pour utilisateurs.
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



    // Logique métier spécifique de l'entité.
    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->paniers = new ArrayCollection(); // 🔥 ajouter ceci
    }
    /**
 * @return Collection<int, Panier>
 */
public function getPaniers(): Collection
{
    return $this->paniers;
}
// Logique métier spécifique de l'entité.
public function addPanier(Panier $panier): static
{
    if (!$this->paniers->contains($panier)) {
        $this->paniers->add($panier);
        $panier->setUtilisateur($this);
    }

    return $this;
}
// Logique métier spécifique de l'entité.
public function removePanier(Panier $panier): static
{
    if ($this->paniers->removeElement($panier)) {
        if ($panier->getUtilisateur() === $this) {
            $panier->setUtilisateur(null);
        }
    }

    return $this;
}

    // Retourne la valeur de id.
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne la valeur de email.
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Met à jour la valeur de email.
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Met à jour la valeur de password.
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    // Logique métier spécifique de l'entité.
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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

    // Retourne la valeur de prenom.
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    // Met à jour la valeur de prenom.
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

    // Logique métier spécifique de l'entité.
    public function addCommande(Commandes $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateurs($this);
        }

        return $this;
    }

    // Logique métier spécifique de l'entité.
    public function removeCommande(Commandes $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateurs() === $this) {
                $commande->setUtilisateurs(null);
            }
        }

        return $this;
    }

    // Indique si verified.
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    // Met à jour la valeur de is verified.
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // Retourne la valeur de deleted at.
    public function getDeletedAt(): ?\DateTimeImmutable
{
    return $this->deletedAt;
}

// Indique si deleted.
public function isDeleted(): bool
{
    return $this->deletedAt !== null;
}
// Logique métier spécifique de l'entité.
public function anonymizeAndDeactivate(): void
{
    $this->deletedAt = new \DateTimeImmutable();

    // anonymisation
    $this->nom = 'Utilisateur';
    $this->prenom = 'supprimé';

    // email doit rester unique (tu as une contrainte UNIQUE)
    // => on génère un email unique basé sur l'id si dispo
    $idPart = $this->id ?? random_int(100000, 999999);
    $this->email = 'deleted_' . $idPart . '@lectis.local';

    // on invalide le password (au cas où)
    $this->password = password_hash(bin2hex(random_bytes(24)), PASSWORD_BCRYPT);

    // optionnel : on remet les rôles
    $this->roles = ['ROLE_USER'];

    // optionnel : on désactive la vérification
    $this->isVerified = false;
}


// Indique si adherent.
public function isAdherent(): bool
{
    return $this->isAdherent;
}

// Retourne la valeur de adherent at.
public function getAdherentAt(): ?\DateTimeImmutable
{
    return $this->adherentAt;
}

// Logique métier spécifique de l'entité.
public function becomeAdherent(): void
{
    $this->isAdherent = true;
    $this->adherentAt = new \DateTimeImmutable();
}

// Logique métier spécifique de l'entité.
public function leaveAdherent(): void
{
    $this->isAdherent = false;
    $this->adherentAt = null;
}

}
