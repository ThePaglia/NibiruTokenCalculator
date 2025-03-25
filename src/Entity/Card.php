<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

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

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private BlobType|null $fullImage = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private BlobType|null $smallImage = null;

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

    public function getFullImage(): BlobType|null
    {
        return $this->fullImage;
    }

    public function setFullImage($fullImage): static
    {
        $this->fullImage = $fullImage;

        return $this;
    }

    public function getSmallImage(): BlobType|null
    {
        return $this->smallImage;
    }

    public function setSmallImage($smallImage): static
    {
        $this->smallImage = $smallImage;

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
}
