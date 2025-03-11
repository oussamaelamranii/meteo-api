<?php

namespace App\Entity;

use App\Repository\LandRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: LandRepository::class)]
class Land
{    
    
    public function __construct()
    {
        $this->plants = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Farm::class, inversedBy: "lands")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Farm $farm = null;

    #[ORM\ManyToMany(targetEntity: Plants::class, inversedBy: "lands")]
    #[ORM\JoinTable(name: "land_plant")]
    private Collection $plants;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): self
    {
        $this->farm = $farm;
        return $this;
    }

    public function getPlants(): Collection {
        return $this->plants;
    }

    public function addPlant(Plants $plant): self {
        if (!$this->plants->contains($plant)) {
            $this->plants[] = $plant;
            $plant->addLand($this);
        }
        return $this;
    }

    public function removePlant(Plants $plant): self {
        if ($this->plants->removeElement($plant)) {
            $plant->removeLand($this);
        }
        return $this;
    }


// ! =================================old======================================



}
