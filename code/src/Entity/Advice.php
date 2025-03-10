<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]

class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //! what plant and land this advice belongs to 

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_en = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_ar = null;

    #[ORM\Column(length: 255)]
    private ?string $AudioPath = null;


    //! this for when we fetch temp from api we check its range here
    #[ORM\Column]
    private ?int $min_temp_C = null;

    #[ORM\Column]
    private ?int $max_temp_C = null;

    //! redAdvice (bool) alerts when aan advice is sensitive by sms , email , notif
    #[ORM\Column]
    private ?bool $RedAlert = false;

//!===================================

    #[ORM\ManyToOne(targetEntity:LandPlants::class, inversedBy:"advices")]
    #[ORM\JoinColumn(name:"land_plant_id", referencedColumnName:"id", onDelete:"CASCADE")]

    private ?LandPlants $landPlant = null;



    // Getter and setter for the landPlant relation
    public function getLandPlant(): ?LandPlants
    {
        return $this->landPlant;
    }

    public function setLandPlant(?LandPlants $landPlant): self
    {
        $this->landPlant = $landPlant;

        return $this;
    }


//!===================================


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getAudioPath(): ?string
    {
        return $this->AudioPath;
    }

    public function setAudioPath(string $AudioPath): static
    {
        $this->AudioPath = $AudioPath;

        return $this;
    }

    public function getMinTempC(): ?int
    {
        return $this->min_temp_C;
    }

    public function setMinTempC(int $min_temp_C): static
    {
        $this->min_temp_C = $min_temp_C;

        return $this;
    }

    public function getMaxTempC(): ?int
    {
        return $this->max_temp_C;
    }

    public function setMaxTempC(int $max_temp_C): static
    {
        $this->max_temp_C = $max_temp_C;

        return $this;
    }

    public function isRedAlert(): ?bool
    {
        return $this->RedAlert;
    }

    public function setRedAlert(bool $RedAlert): static
    {
        $this->RedAlert = $RedAlert;

        return $this;
    }

    
}
