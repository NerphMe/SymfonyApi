<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 100)]
    private string $surname;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $hiredAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $currentSalaryAmount;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHiredAt(): \DateTimeInterface
    {
        return $this->hiredAt;
    }

    public function getCurrentSalaryAmount(): string
    {
        return $this->currentSalaryAmount;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    // Сеттери
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setHiredAt(\DateTimeInterface $hiredAt): self
    {
        $this->hiredAt = $hiredAt;

        return $this;
    }

    public function setCurrentSalaryAmount(string $currentSalaryAmount): self
    {
        $this->currentSalaryAmount = $currentSalaryAmount;

        return $this;
    }
}
