<?php

namespace App\Entity;

use App\Repository\EmployeeRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRoleRepository::class)]
class EmployeeRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee_fkid = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company_fkid = null;

    #[ORM\Column(length: 150)]
    private ?string $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeFkid(): ?Employee
    {
        return $this->employee_fkid;
    }

    public function setEmployeeFkid(Employee $employee_fkid): self
    {
        $this->employee_fkid = $employee_fkid;

        return $this;
    }

    public function getCompanyFkid(): ?Company
    {
        return $this->company_fkid;
    }

    public function setCompanyFkid(?Company $company_fkid): self
    {
        $this->company_fkid = $company_fkid;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
