<?php

namespace App\Entity;

use App\Enum\GrowthStage;
use App\Repository\UserPlantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPlantsRepository::class)]
class UserPlants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    #[ORM\ManyToMany(targetEntity: Plants::class, inversedBy: 'plants')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?int $plant_id = null;

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

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

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
}
