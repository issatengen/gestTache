<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SubTaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubTaskRepository::class)]
class SubTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: "today", 
        message: 'La date de début doit être aujourd\'hui ou dans le futur.'
    )]
    private ?\DateTime $debut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: "today", 
        message: 'La date de fin doit être aujourd\'hui ou dans le futur.'
    )]
    private ?\DateTime $fin = null;

    #[ORM\ManyToOne(inversedBy: 'subTask')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'subTasks')]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $timeAllocated = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDebut(): ?\DateTime
    {
        return $this->debut;
    }

    public function setDebut(\DateTime $debut): static
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setFin(?\DateTime $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user; 

        return $this;
    }

    public function getTimeAllocated(): ?float
    {
        return $this->timeAllocated;
    }

    public function setTimeAllocated(float $timeAllocated): static
    {
        $this->timeAllocated = $timeAllocated;

        return $this;
    }
}
