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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $ideal_temp_min = null;

    #[ORM\Column]
    private ?float $ideal_temp_max = null;

    #[ORM\Column]
    private ?float $ideal_moisture_min = null;

    #[ORM\Column]
    private ?float $ideal_moisture_max = null;

    #[ORM\Column]
    private ?float $ideal_ph_min = null;

    #[ORM\Column]
    private ?float $ideal_ph_max = null;

    #[ORM\Column(enumType: SunlightRequirement::class)]
    private ?SunlightRequirement $sunlight_requirement = null;

    #[ORM\Column]
    private ?int $safe_min_temp_C = null;

    #[ORM\Column]
    private ?int $safe_max_temp_C = null;

    #[ORM\OneToMany(mappedBy: "plant", targetEntity: LandPlants::class)]
    private Collection $landPlants;

    public function __construct()
    {
        $this->landPlants = new ArrayCollection();
    }

    public function getLandPlants(): Collection
    {
        return $this->landPlants;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getIdealTempMin(): ?float
    {
        return $this->ideal_temp_min;
    }

    public function setIdealTempMin(float $ideal_temp_min): static
    {
        $this->ideal_temp_min = $ideal_temp_min;

        return $this;
    }

    public function getIdealTempMax(): ?float
    {
        return $this->ideal_temp_max;
    }

    public function setIdealTempMax(float $ideal_temp_max): static
    {
        $this->ideal_temp_max = $ideal_temp_max;

        return $this;
    }

    public function getIdealMoistureMin(): ?float
    {
        return $this->ideal_moisture_min;
    }

    public function setIdealMoistureMin(float $ideal_moisture_min): static
    {
        $this->ideal_moisture_min = $ideal_moisture_min;

        return $this;
    }

    public function getIdealMoistureMax(): ?float
    {
        return $this->ideal_moisture_max;
    }

    public function setIdealMoistureMax(float $ideal_moisture_max): static
    {
        $this->ideal_moisture_max = $ideal_moisture_max;

        return $this;
    }

    public function getIdealPhMin(): ?float
    {
        return $this->ideal_ph_min;
    }

    public function setIdealPhMin(float $ideal_ph_min): static
    {
        $this->ideal_ph_min = $ideal_ph_min;

        return $this;
    }

    public function getIdealPhMax(): ?float
    {
        return $this->ideal_ph_max;
    }

    public function setIdealPhMax(float $ideal_ph_max): static
    {
        $this->ideal_ph_max = $ideal_ph_max;

        return $this;
    }

    public function getSunlightRequirement(): ?SunlightRequirement
    {
        return $this->sunlight_requirement;
    }

    public function setSunlightRequirement(SunlightRequirement $sunlight_requirement): static
    {
        $this->sunlight_requirement = $sunlight_requirement;

        return $this;
    }

    public function getSafeMinTempC(): ?int
    {
        return $this->safe_min_temp_C;
    }

    public function setSafeMinTempC(int $safe_min_temp_C): static
    {
        $this->safe_min_temp_C = $safe_min_temp_C;

        return $this;
    }

    public function getSafeMaxTempC(): ?int
    {
        return $this->safe_max_temp_C;
    }

    public function setSafeMaxTempC(int $safe_max_temp_C): static
    {
        $this->safe_max_temp_C = $safe_max_temp_C;

        return $this;
    }
}
