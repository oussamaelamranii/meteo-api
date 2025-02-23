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
    private ?string $advice_text_en = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_ar = null;

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

    public function getAdviceTextEn(): ?string
    {
        return $this->advice_text_en;
    }

    public function setAdviceTextEn(string $advice_text_en): static
    {
        $this->advice_text_en = $advice_text_en;

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

    public function getAdviceTextFr(): ?string
    {
        return $this->advice_text_fr;
    }

    public function setAdviceTextFr(string $advice_text_fr): static
    {
        $this->advice_text_fr = $advice_text_fr;

        return $this;
    }

    public function getAdviceTextAr(): ?string
    {
        return $this->advice_text_ar;
    }

    public function setAdviceTextAr(string $advice_text_ar): static
    {
        $this->advice_text_ar = $advice_text_ar;

        return $this;
    }
}
