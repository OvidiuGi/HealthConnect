<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: ServiceRepository::class)]
#[Table(name: '`service`')]
class Service
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    public string $name = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $price = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $description = '';

    // One Doctor has many Schedules.
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: 'doctor_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $doctor;

    #[ORM\Column(type: 'integer', length: 256, unique: false)]
    public int $duration = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDoctor(): User
    {
        return $this->doctor;
    }

    public function setDoctor(User $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }
}