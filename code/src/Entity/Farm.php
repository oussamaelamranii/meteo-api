<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FarmRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
class Farm
{

    public function __construct()
    {
        $this->lands = new ArrayCollection();
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\OneToMany(targetEntity: Land::class, mappedBy: 'farm')]
    private Collection $lands;


    
    public function getLands(): Collection
    {
        return $this->lands;
    }

    public function addLand(Land $land): self
    {
        if (!$this->lands->contains($land)) {
            $this->lands->add($land);
            $land->setFarm($this);
        }

        return $this;
    }

    public function removeLand(Land $land): self
    {
        if ($this->lands->removeElement($land)) {
            // Set the owning side to null (unless already changed)
            if ($land->getFarm() === $this) {
                $land->setFarm(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    

}
