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
    private ?string $smallImageURL = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fr_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $de_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $it_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pt_name = null;

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

    public function getSmallImageURL(): ?string
    {
        return $this->smallImageURL;
    }

    public function setSmallImageURL(?string $smallImageURL): static
    {
        $this->smallImageURL = $smallImageURL;

        return $this;
    }

    public function getFrName(): ?string
    {
        return $this->fr_name;
    }

    public function setFrName(string $fr_name): static
    {
        $this->fr_name = $fr_name;

        return $this;
    }

    public function getDeName(): ?string
    {
        return $this->de_name;
    }

    public function setDeName(string $de_name): static
    {
        $this->de_name = $de_name;

        return $this;
    }

    public function getItName(): ?string
    {
        return $this->it_name;
    }

    public function setItName(string $it_name): static
    {
        $this->it_name = $it_name;

        return $this;
    }

    public function getPtName(): ?string
    {
        return $this->pt_name;
    }

    public function setPtName(string $pt_name): static
    {
        $this->pt_name = $pt_name;

        return $this;
    }
}
