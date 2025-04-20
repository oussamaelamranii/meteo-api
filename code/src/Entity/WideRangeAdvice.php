<?php

namespace App\Entity;

use App\Repository\WideRangeAdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WideRangeAdviceRepository::class)]
class WideRangeAdvice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $UserId = null;

    #[ORM\Column]
    private ?int $LandId = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    private ?string $areaAffected = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_en = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_ar = null;

    #[ORM\Column(length: 255)]
    private ?string $AudioPathAr = null;

    #[ORM\Column(length: 255)]
    private ?string $AudioPathFr = null;

    #[ORM\Column(length: 255)]
    private ?string $AudioPathEn = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $advice_date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->UserId;
    }

    public function setUserId(int $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }

    public function getLandId(): ?int
    {
        return $this->LandId;
    }

    public function setLandId(int $LandId): static
    {
        $this->LandId = $LandId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getAreaAffected(): ?string
    {
        return $this->areaAffected;
    }

    public function setAreaAffected(string $areaAffected): static
    {
        $this->areaAffected = $areaAffected;

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

    public function getAudioPathAr(): ?string
    {
        return $this->AudioPathAr;
    }

    public function setAudioPathAr(string $AudioPathAr): static
    {
        $this->AudioPathAr = $AudioPathAr;

        return $this;
    }

    public function getAudioPathFr(): ?string
    {
        return $this->AudioPathFr;
    }

    public function setAudioPathFr(string $AudioPathFr): static
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

    public function getAdviceDate(): ?\DateTimeInterface
    {
        return $this->advice_date;
    }

    public function setAdviceDate(\DateTimeInterface $advice_date): static
    {
        $this->advice_date = $advice_date;

        return $this;
    }
}
