<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $espece = null; // Chien, Chat, Lapin, Oiseau

    #[ORM\Column(length: 100)]
    private ?string $race = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null; // Mâle, Femelle

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $numeroPuce = null;

    #[ORM\Column]
    private bool $vaccine = true;

    #[ORM\Column]
    private bool $sterilise = true;

    #[ORM\OneToOne(inversedBy: 'animal')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEspece(): ?string
    {
        return $this->espece;
    }

    public function setEspece(string $espece): static
    {
        $this->espece = $espece;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getNumeroPuce(): ?string
    {
        return $this->numeroPuce;
    }

    public function setNumeroPuce(string $numeroPuce): static
    {
        $this->numeroPuce = $numeroPuce;

        return $this;
    }

    public function isVaccine(): bool
    {
        return $this->vaccine;
    }

    public function setVaccine(bool $vaccine): static
    {
        $this->vaccine = $vaccine;

        return $this;
    }

    public function isSterilise(): bool
    {
        return $this->sterilise;
    }

    public function setSterilise(bool $sterilise): static
    {
        $this->sterilise = $sterilise;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getAge(): string
    {
        if (!$this->dateNaissance) {
            return '';
        }

        $now = new \DateTime();
        $diff = $now->diff($this->dateNaissance);

        if ($diff->y > 0) {
            return $diff->y . ' an' . ($diff->y > 1 ? 's' : '') . ($diff->m > 0 ? ' et ' . $diff->m . ' mois' : '');
        }

        return $diff->m . ' mois';
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->race, $this->espece);
    }
}
