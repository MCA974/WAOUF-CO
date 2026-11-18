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
    private ?string $espece = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $race = null;

    #[ORM\Column(length: 20)]
    private ?string $sexe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $numeroPuce = null;

    #[ORM\Column]
    private ?bool $vaccin = null;

    #[ORM\Column]
    private ?bool $sterilise = null;

    #[ORM\OneToOne(mappedBy: 'animal')]
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

    public function setRace(?string $race): static
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

    public function setNumeroPuce(?string $numeroPuce): static
    {
        $this->numeroPuce = $numeroPuce;

        return $this;
    }

    public function isVaccin(): ?bool
    {
        return $this->vaccin;
    }

    public function setVaccin(bool $vaccin): static
    {
        $this->vaccin = $vaccin;

        return $this;
    }

    public function isSterilise(): ?bool
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
        // unset the owning side of the relationship if necessary
        if ($article === null && $this->article !== null) {
            $this->article->setAnimal(null);
        }

        // set the owning side of the relationship if necessary
        if ($article !== null && $article->getAnimal() !== $this) {
            $article->setAnimal($this);
        }

        $this->article = $article;

        return $this;
    }
}
