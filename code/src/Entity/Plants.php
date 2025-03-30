<?php

namespace App\Entity;

use App\Enum\SunlightRequirement;
use App\Repository\PlantsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: PlantsRepository::class)]
class Plants
{

    public function __construct()
    {
        $this->lands = new ArrayCollection();
        $this->advices = new ArrayCollection();
        $this->generalAdvices = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Land::class, mappedBy: "plants")]
    private Collection $lands;

    #[ORM\OneToMany(mappedBy: "plant", targetEntity: Advice::class)]
    private Collection $advices;

    #[ORM\OneToMany(targetEntity: GeneralAdvice::class, mappedBy: "plant")]
    private Collection $generalAdvices;

    #[ORM\Column(nullable: true)]
    private ?float $safeMinHumidity = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMaxHumidity = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMinPrecipitation = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMaxPrecipitation = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMinWindSpeed = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMaxWindSpeed = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMinTempC = null;

    #[ORM\Column(nullable: true)]
    private ?float $safeMaxTempC = null;



// ?============================
    public function getId(): ?int {
        return $this->id;
    }

    public function getLands(): Collection {
        return $this->lands;
    }

    public function addLand(Land $land): self {
        if (!$this->lands->contains($land)) {
            $this->lands[] = $land;
        }
        return $this;
    }

    public function removeLand(Land $land): self {
        $this->lands->removeElement($land);
        return $this;
    }

    public function getAdvices(): Collection {
        return $this->advices;
    }

    public function addAdvice(Advice $advice): self {
        if (!$this->advices->contains($advice)) {
            $this->advices[] = $advice;
            $advice->setPlant($this);
        }
        return $this;
    }

    public function removeAdvice(Advice $advice): self {
        if ($this->advices->removeElement($advice)) {
            if ($advice->getPlant() === $this) {
                $advice->setPlant(null);
            }
        }
        return $this;
    }

    public function getGeneralAdvices(): Collection
    {
        return $this->generalAdvices;
    }

    public function addGeneralAdvice(GeneralAdvice $generalAdvice): self
    {
        if (!$this->generalAdvices->contains($generalAdvice)) {
            $this->generalAdvices->add($generalAdvice);
            $generalAdvice->setPlant($this);
        }
        return $this;
    }

    public function removeGeneralAdvice(GeneralAdvice $generalAdvice): self
    {
        if ($this->generalAdvices->removeElement($generalAdvice)) {
            // Set the plant to null if it was linked to this plant
            if ($generalAdvice->getPlant() === $this) {
                $generalAdvice->setPlant(null);
            }
        }
        return $this;
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

    public function getSafeMaxTempC(): ?int
    {
        return $this->safeMaxTempC;
    }

    public function setSafeMaxTempC(int $safeMaxTempC): static
    {
        $this->safeMaxTempC = $safeMaxTempC;

        return $this;
    }

    public function getSafeMinTempC(): ?int
    {
        return $this->safeMinTempC;
    }

    public function setSafeMinTempC(int $safeMinTempC): static
    {
        $this->safeMinTempC = $safeMinTempC;

        return $this;
    }

















// ! =================================old======================================


    // #[ORM\Column]
    // private ?float $ideal_temp_min = null;

    // #[ORM\Column]
    // private ?float $ideal_temp_max = null;

    // #[ORM\Column]
    // private ?float $ideal_moisture_min = null;

    // #[ORM\Column]
    // private ?float $ideal_moisture_max = null;

    // #[ORM\Column]
    // private ?float $ideal_ph_min = null;

    // #[ORM\Column]
    // private ?float $ideal_ph_max = null;

    // #[ORM\Column(enumType: SunlightRequirement::class)]
    // private ?SunlightRequirement $sunlight_requirement = null;


    // #[ORM\OneToMany(mappedBy: "plant", targetEntity: LandPlants::class)]
    // private Collection $landPlants;

    // public function __construct()
    // {
    //     $this->landPlants = new ArrayCollection();
    // }

    // public function getLandPlants(): Collection
    // {
    //     return $this->landPlants;
    // }

    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    // public function setId(int $id): static
    // {
    //     $this->id = $id;

    //     return $this;
    // }



    // public function getIdealTempMin(): ?float
    // {
    //     return $this->ideal_temp_min;
    // }

    // public function setIdealTempMin(float $ideal_temp_min): static
    // {
    //     $this->ideal_temp_min = $ideal_temp_min;

    //     return $this;
    // }

    // public function getIdealTempMax(): ?float
    // {
    //     return $this->ideal_temp_max;
    // }

    // public function setIdealTempMax(float $ideal_temp_max): static
    // {
    //     $this->ideal_temp_max = $ideal_temp_max;

    //     return $this;
    // }

    // public function getIdealMoistureMin(): ?float
    // {
    //     return $this->ideal_moisture_min;
    // }

    // public function setIdealMoistureMin(float $ideal_moisture_min): static
    // {
    //     $this->ideal_moisture_min = $ideal_moisture_min;

    //     return $this;
    // }

    // public function getIdealMoistureMax(): ?float
    // {
    //     return $this->ideal_moisture_max;
    // }

    // public function setIdealMoistureMax(float $ideal_moisture_max): static
    // {
    //     $this->ideal_moisture_max = $ideal_moisture_max;

    //     return $this;
    // }

    // public function getIdealPhMin(): ?float
    // {
    //     return $this->ideal_ph_min;
    // }

    // public function setIdealPhMin(float $ideal_ph_min): static
    // {
    //     $this->ideal_ph_min = $ideal_ph_min;

    //     return $this;
    // }

    // public function getIdealPhMax(): ?float
    // {
    //     return $this->ideal_ph_max;
    // }

    // public function setIdealPhMax(float $ideal_ph_max): static
    // {
    //     $this->ideal_ph_max = $ideal_ph_max;

    //     return $this;
    // }

    // public function getSunlightRequirement(): ?SunlightRequirement
    // {
    //     return $this->sunlight_requirement;
    // }

    // public function setSunlightRequirement(SunlightRequirement $sunlight_requirement): static
    // {
    //     $this->sunlight_requirement = $sunlight_requirement;

    //     return $this;
    // }

    public function getSafeMinHumidity(): ?float
    {
        return $this->safeMinHumidity;
    }

    public function setSafeMinHumidity(?float $safeMinHumidity): static
    {
        $this->safeMinHumidity = $safeMinHumidity;

        return $this;
    }

    public function getSafeMaxHumidity(): ?float
    {
        return $this->safeMaxHumidity;
    }

    public function setSafeMaxHumidity(?float $safeMaxHumidity): static
    {
        $this->safeMaxHumidity = $safeMaxHumidity;

        return $this;
    }

    public function getSafeMinPrecipitation(): ?float
    {
        return $this->safeMinPrecipitation;
    }

    public function setSafeMinPrecipitation(?float $safeMinPrecipitation): static
    {
        $this->safeMinPrecipitation = $safeMinPrecipitation;

        return $this;
    }

    public function getSafeMaxPrecipitation(): ?float
    {
        return $this->safeMaxPrecipitation;
    }

    public function setSafeMaxPrecipitation(?float $safeMaxPrecipitation): static
    {
        $this->safeMaxPrecipitation = $safeMaxPrecipitation;

        return $this;
    }

    public function getSafeMinWindSpeed(): ?float
    {
        return $this->safeMinWindSpeed;
    }

    public function setSafeMinWindSpeed(?float $safeMinWindSpeed): static
    {
        $this->safeMinWindSpeed = $safeMinWindSpeed;

        return $this;
    }

    public function getSafeMaxWindSpeed(): ?float
    {
        return $this->safeMaxWindSpeed;
    }

    public function setSafeMaxWindSpeed(?float $safeMaxWindSpeed): static
    {
        $this->safeMaxWindSpeed = $safeMaxWindSpeed;

        return $this;
    }




}
