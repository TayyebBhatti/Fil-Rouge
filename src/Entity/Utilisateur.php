<?php
namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: "Utilisateur")]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_utilisateur", type: "integer")]
    private ?int $idUtilisateur = null;

    #[ORM\Column(name: "nom", type: "string", length: 100)]
    private string $nom;

    #[ORM\Column(name: "prenom", type: "string", length: 100)]
    private string $prenom;

    #[ORM\Column(name: "email", type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(name: "mot_de_passe", type: "string", length: 255)]
    private string $motDePasse;

    #[ORM\Column(name: "role", type: "json")]
    private array $role = ['ROLE_USER'];

    /**
     * @var Collection<int, Inscription>
     */
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'utilisateur')]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }


   public function getIdUtilisateur(): ?int
    {
    return $this->idUtilisateur;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $hashedPassword): self
    {
        $this->motDePasse = $hashedPassword;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = $this->role ?? [];
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->role = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function __toString(): string
    {
        return $this->email ?? '';
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setUtilisateur($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getUtilisateur() === $this) {
                $inscription->setUtilisateur(null);
            }
        }

        return $this;
    }
}
