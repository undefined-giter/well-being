<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfessionalRepository;

#[ORM\Entity(repositoryClass: ProfessionalRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorMap(["user" => User::class, "professional" => Professional::class])]
class Professional extends User
{
    #[ORM\Column(type: Types::ARRAY)]
    private array $specialization = [];

    #[ORM\Column(nullable: true)]
    private ?array $location = null;

    #[ORM\Column]
    private ?bool $online_availability = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    public function getSpecialization(): array
    {
        return $this->specialization;
    }

    public function setSpecialization(array $specialization): static
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getLocation(): ?array
    {
        return $this->location;
    }

    public function setLocation(?array $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function isOnlineAvailability(): ?bool
    {
        return $this->online_availability;
    }

    public function setOnlineAvailability(bool $online_availability): static
    {
        $this->online_availability = $online_availability;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): static
    {
        $this->video = $video;

        return $this;
    }
}
