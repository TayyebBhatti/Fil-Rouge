<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
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
    private ?Lieu $lieu = null;

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
}
