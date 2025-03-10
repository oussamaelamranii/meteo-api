<?php

namespace App\Entity;

use App\Enum\GrowthStage;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LandPlantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: LandPlantsRepository::class)]
class LandPlants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Land::class, inversedBy: 'userPlants')]
    #[ORM\JoinColumn(name: 'land_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?int $land_id = null;

    #[ORM\Column]
    #[ORM\ManyToMany(targetEntity: Plants::class, inversedBy: 'plants')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?int $plant_id = null;

    #[ORM\OneToMany(mappedBy: "landPlant", targetEntity: Advice::class, cascade: ["persist", "remove"])]
    private Collection $advices;

    public function __construct()
    {
        $this->advices = new ArrayCollection();
    }

    public function getAdvices(): Collection
    {
        return $this->advices;
    }

    //! should we move current_temp ?? yes / put DTO

    #[ORM\Column]
    private ?float $current_temp = null;

    #[ORM\Column]
    private ?float $soil_moisture = null;

    #[ORM\Column]
    private ?float $soil_ph = null;

    #[ORM\Column(enumType: GrowthStage::class)]
    private ?GrowthStage $growth_stage = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $last_updated = null;


//!===================================
    #[ORM\ManyToOne(targetEntity:Plants::class, inversedBy:"landPlants")]
    #[ORM\JoinColumn(name:"plant_id", referencedColumnName:"id", onDelete:"CASCADE")]

    private ?Plants $plant = null;

    // Getter and setter for the plant relation
    public function getPlant(): ?Plants
    {
        return $this->plant;
    }

    public function setPlant(?Plants $plant): self
    {
        $this->plant = $plant;

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


    public function getPlantId(): ?int
    {
        return $this->plant_id;
    }

    public function setPlantId(int $plant_id): static
    {
        $this->plant_id = $plant_id;

        return $this;
    }

    public function getCurrentTemp(): ?float
    {
        return $this->current_temp;
    }

    public function setCurrentTemp(float $current_temp): static
    {
        $this->current_temp = $current_temp;

        return $this;
    }

    public function getSoilMoisture(): ?float
    {
        return $this->soil_moisture;
    }

    public function setSoilMoisture(float $soil_moisture): static
    {
        $this->soil_moisture = $soil_moisture;

        return $this;
    }

    public function getSoilPh(): ?float
    {
        return $this->soil_ph;
    }

    public function setSoilPh(float $soil_ph): static
    {
        $this->soil_ph = $soil_ph;

        return $this;
    }

    public function getGrowthStage(): ?GrowthStage
    {
        return $this->growth_stage;
    }

    public function setGrowthStage(GrowthStage $growth_stage): static
    {
        $this->growth_stage = $growth_stage;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeImmutable
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeImmutable $last_updated): static
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    public function getLandId(): ?int
    {
        return $this->land_id;
    }

    public function setLandId(int $land_id): static
    {
        $this->land_id = $land_id;

        return $this;
    }
}
