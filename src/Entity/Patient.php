<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PatientRepository;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient extends User
{    
    #[ORM\Column]
    private ?bool $is_followed = false;

    public function __construct()
    {
        parent::__construct();
        $this->addRoles(['patient']);
    }

    public function getIsFollowed(): ?bool
    {
        return $this->is_followed;
    }

    public function setIsFollowed(bool $is_followed): static
    {
        $this->is_followed = $is_followed;

        return $this;
    }
}
