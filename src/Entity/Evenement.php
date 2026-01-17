<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    /** @phpstan-ignore-next-line */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: 'La date de debut est obligatoire.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotNull(message: 'La capacite max est obligatoire.')]
    #[Assert\Positive(message: 'La capacite max doit etre positive.')]
    private ?int $capaciteMax = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'evenementsCrees')]
    #[ORM\JoinColumn(name: 'createur_id', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $createur = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id', nullable: true)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Inscription::class, cascade: ['remove'])]
    private Collection $inscriptions;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le lieu est obligatoire.')]
    private ?Lieu $lieu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'image est obligatoire.")]
    #[Assert\Length(max: 255)]
    private string $image = 'img/default.jpg';

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): self { $this->dateFin = $dateFin; return $this; }

    public function getCapaciteMax(): ?int { return $this->capaciteMax; }
    public function setCapaciteMax(?int $capaciteMax): self { $this->capaciteMax = $capaciteMax; return $this; }

    public function getCreateur(): ?Utilisateur { return $this->createur; }
    public function setCreateur(?Utilisateur $createur): self { $this->createur = $createur; return $this; }

    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): self { $this->categorie = $categorie; return $this; }

    /** @return Collection<int, Inscription> */
    public function getInscriptions(): Collection { return $this->inscriptions; }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $image = trim((string) $image);
        $this->image = $image !== '' ? $image : 'img/default.jpg';

        return $this;
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if (!$this->dateDebut instanceof \DateTimeInterface || !$this->dateFin instanceof \DateTimeInterface) {
            return;
        }

        if ($this->dateFin < $this->dateDebut) {
            $context
                ->buildViolation('La date de fin doit etre posterieure ou egale a la date de debut.')
                ->atPath('dateFin')
                ->addViolation();
        }
    }
}
