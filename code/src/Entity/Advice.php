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
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Land::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Land $land = null;

    #[ORM\ManyToOne(targetEntity: Plants::class, inversedBy: "advices")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Plants $plant = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_en = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_fr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice_text_ar = null;

    //! audio fr et ang ===========================
    #[ORM\Column(length: 255)]
    private ?string $AudioPathAr = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AudioPathFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AudioPathEn = null;

    

    //! redAdvice (bool) alerts when aan advice is sensitive by sms , email , notif
    #[ORM\Column]
    private ?bool $RedAlert = false;

    
//! ====== Soil - midite - wind - rain - dbab =======
    //! this for when we fetch temp from api we check its range here
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $min_temp_C = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $max_temp_C = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $min_humidity = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $max_humidity = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $min_precipitation = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $max_precipitation = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $min_wind_speed = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $max_wind_speed = null;


    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $advice_date = null;




    public function getId(): ?int {
        return $this->id;
    }

    // Getter
    public function getAdviceDate(): ?\DateTime
    {
        return $this->advice_date;
    }

    // Setter
    public function setAdviceDate(?\DateTime $advice_date): self
    {
        $this->advice_date = $advice_date;
        return $this;
    }

    public function getLand(): ?Land {
        return $this->land;
    }

    public function setLand(?Land $land): self {
        $this->land = $land;
        return $this;
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

    public function getAudioPathAr(): ?string
    {
        return $this->AudioPathAr;
    }

    public function setAudioPathAr(string $AudioPathAr): static
    {
        $this->AudioPathAr = $AudioPathAr;

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

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
// ! =================================old======================================
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     //! what plant and land this advice belongs to 

//     #[ORM\Column(type: Types::TEXT)]
//     private ?string $advice_text_en = null;

//     #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
//     private ?\DateTimeImmutable $created_at = null;

//     #[ORM\Column(type: Types::TEXT)]
//     private ?string $advice_text_fr = null;

//     #[ORM\Column(type: Types::TEXT)]
//     private ?string $advice_text_ar = null;

//     #[ORM\Column(length: 255)]
//     private ?string $AudioPath = null;


//     //! this for when we fetch temp from api we check its range here
//     #[ORM\Column]
//     private ?int $min_temp_C = null;

//     #[ORM\Column]
//     private ?int $max_temp_C = null;

//     //! redAdvice (bool) alerts when aan advice is sensitive by sms , email , notif
//     #[ORM\Column]
//     private ?bool $RedAlert = false;

// //!===================================

//     #[ORM\ManyToOne(targetEntity:LandPlants::class, inversedBy:"advices")]
//     #[ORM\JoinColumn(name:"land_plant_id", referencedColumnName:"id", onDelete:"CASCADE")]

//     private ?LandPlants $landPlant = null;



//     // Getter and setter for the landPlant relation
//     public function getLandPlant(): ?LandPlants
//     {
//         return $this->landPlant;
//     }

//     public function setLandPlant(?LandPlants $landPlant): self
//     {
//         $this->landPlant = $landPlant;

//         return $this;
//     }


// //!===================================


//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function setId(int $id): static
//     {
//         $this->id = $id;

//         return $this;
//     }


//     

public function getMinHumidity(): ?float
{
    return $this->min_humidity;
}

public function setMinHumidity(float $min_humidity): static
{
    $this->min_humidity = $min_humidity;

    return $this;
}

public function getMaxHumidity(): ?float
{
    return $this->max_humidity;
}

public function setMaxHumidity(float $max_humidity): static
{
    $this->max_humidity = $max_humidity;

    return $this;
}

public function getMinPrecipitation(): ?float
{
    return $this->min_precipitation;
}

public function setMinPrecipitation(float $min_precipitation): static
{
    $this->min_precipitation = $min_precipitation;

    return $this;
}

public function getMaxPrecipitation(): ?float
{
    return $this->max_precipitation;
}

public function setMaxPrecipitation(float $max_precipitation): static
{
    $this->max_precipitation = $max_precipitation;

    return $this;
}

public function getMinWindSpeed(): ?float
{
    return $this->min_wind_speed;
}

public function setMinWindSpeed(float $min_wind_speed): static
{
    $this->min_wind_speed = $min_wind_speed;

    return $this;
}

public function getMaxWindSpeed(): ?float
{
    return $this->max_wind_speed;
}

public function setMaxWindSpeed(float $max_wind_speed): static
{
    $this->max_wind_speed = $max_wind_speed;

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

public function setAudioPathEn(?string $AudioPathEn): static
{
    $this->AudioPathEn = $AudioPathEn;

    return $this;
}
    
}
