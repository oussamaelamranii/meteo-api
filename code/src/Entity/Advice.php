<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]
// #[ApiResource]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_plant_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserPlantId(): ?int
    {
        return $this->user_plant_id;
    }

    public function setUserPlantId(int $user_plant_id): static
    {
        $this->user_plant_id = $user_plant_id;

        return $this;
    }

    public function getAdviceText(): ?string
    {
        return $this->advice_text;
    }

    public function setAdviceText(string $advice_text): static
    {
        $this->advice_text = $advice_text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
