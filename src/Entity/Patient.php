<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PatientRepository;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name:"user_type", type:"string")]
#[ORM\DiscriminatorMap(["user" => User::class, "patient" => Patient::class])]
class Patient extends User
{
    #[ORM\Column]
    private ?bool $is_followed = null;

    public function isIsFollowed(): ?bool
    {
        return $this->is_followed;
    }

    public function setIsFollowed(bool $is_followed): static
    {
        $this->is_followed = $is_followed;

        return $this;
    }
}
