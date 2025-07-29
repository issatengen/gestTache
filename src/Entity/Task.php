<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $designation = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: "today", 
        message: 'La date de début doit être aujourd\'hui ou dans le futur.'
    )]
    private ?\DateTime $debut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: "today", 
        message: 'La date de fin doit être aujourd\'hui ou dans le futur.'
    )]
    private ?\DateTime $fin = null;

    /**
     * @var Collection<int, SubTask>
     */
    #[ORM\OneToMany(targetEntity: SubTask::class, mappedBy: 'task')]
    private Collection $subTask;

    /**
     * @var Collection<int, Week>
     */
    #[ORM\ManyToMany(targetEntity: Week::class, mappedBy: 'tasks')]
    private Collection $task;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tasks')]
    private Collection $user;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Department $department = null;

    public function __construct()
    {
        $this->subTask = new ArrayCollection();
        $this->task = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

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

    /**
     * @return Collection<int, SubTask>
     */
    public function getSubTask(): Collection
    {
        return $this->subTask;
    }

    public function addSubTask(SubTask $subTask): static
    {
        if (!$this->subTask->contains($subTask)) {
            $this->subTask->add($subTask);
            $subTask->setTask($this);
        }

        return $this;
    }

    public function removeSubTask(SubTask $subTask): static
    {
        if ($this->subTask->removeElement($subTask)) {
            // set the owning side to null (unless already changed)
            if ($subTask->getTask() === $this) {
                $subTask->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Week>
     */
    public function getTask(): Collection
    {
        return $this->task;
    }

    public function addTask(Week $task): static
    {
        if (!$this->task->contains($task)) {
            $this->task->add($task);
            $task->addTask($this);
        }

        return $this;
    }

    public function removeTask(Week $task): static
    {
        if ($this->task->removeElement($task)) {
            $task->removeTask($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }
}
