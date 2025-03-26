<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $atk = null;

    #[ORM\Column(nullable: true)]
    private ?int $def = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullImageURL = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $smallImageURL = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAtk(): ?int
    {
        return $this->atk;
    }

    public function setAtk(?int $atk): static
    {
        $this->atk = $atk;

        return $this;
    }

    public function getDef(): ?int
    {
        return $this->def;
    }

    public function setDef(?int $def): static
    {
        $this->def = $def;

        return $this;
    }

    public function getFullImageURL(): ?string
    {
        return $this->fullImageURL;
    }

    public function setFullImageURL(?string $fullImageURL): static
    {
        $this->fullImageURL = $fullImageURL;

        return $this;
    }

    public function getSmallImageURL(): ?string
    {
        return $this->smallImageURL;
    }

    public function setSmallImageURL(?string $smallImageURL): static
    {
        $this->smallImageURL = $smallImageURL;

        return $this;
    }
}
