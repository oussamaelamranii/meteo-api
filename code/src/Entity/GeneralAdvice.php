<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GeneralAdviceRepository;

#[ORM\Entity(repositoryClass: GeneralAdviceRepository::class)]
class GeneralAdvice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plants::class, inversedBy: "generalAdvices")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Plants $plant = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_en = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_ar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AudioPathAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AudioPathFr = null;

    #[ORM\Column(length: 255)]
    private ?string $AudioPathEn = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlant(): ?Plants {
        return $this->plant;
    }

    public function setPlant(?Plants $plant): self {
        $this->plant = $plant;
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

    public function getAdviceTextFr(): ?string
    {
        return $this->advice_text_fr;
    }

    public function setAdviceTextFr(?string $advice_text_fr): static
    {
        $this->advice_text_fr = $advice_text_fr;

        return $this;
    }

    public function getAdviceTextAr(): ?string
    {
        return $this->advice_text_ar;
    }

    public function setAdviceTextAr(?string $advice_text_ar): static
    {
        $this->advice_text_ar = $advice_text_ar;

        return $this;
    }

    public function getAudioPathAr(): ?string
    {
        return $this->AudioPathAr;
    }

    public function setAudioPathAr(?string $AudioPathAr): static
    {
        $this->AudioPathAr = $AudioPathAr;

        return $this;
    }

    public function getAudioPathFr(): ?string
    {
        return $this->AudioPathFr;
    }

    public function setAudioPathFr(?string $AudioPathFr): static
    {
        $this->AudioPathFr = $AudioPathFr;

        return $this;
    }

    public function getAudioPathEn(): ?string
    {
        return $this->AudioPathEn;
    }

    public function setAudioPathEn(string $AudioPathEn): static
    {
        $this->AudioPathEn = $AudioPathEn;

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
