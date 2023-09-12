<?php

namespace App\Entity;

use App\Repository\SocialMediaEmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialMediaEmployeeRepository::class)]
class SocialMediaEmployee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Employee $employee_fkid = null;

    #[ORM\ManyToOne]
    private ?SocialMedia $social_media_fkid = null;

    #[ORM\Column(length: 150)]
    private ?string $username = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeFkid(): ?Employee
    {
        return $this->employee_fkid;
    }

    public function setEmployeeFkid(?Employee $employee_fkid): self
    {
        $this->employee_fkid = $employee_fkid;

        return $this;
    }

    public function getSocialMediaFkid(): ?SocialMedia
    {
        return $this->social_media_fkid;
    }

    public function setSocialMediaFkid(?SocialMedia $social_media_fkid): self
    {
        $this->social_media_fkid = $social_media_fkid;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
