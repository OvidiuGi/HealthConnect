<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $name = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $price = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $description = '';

    // Many Services have Many Doctors.
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'services')]
    #[ORM\JoinTable(name: 'services_doctors')]
    private Collection $doctors;

    #[ORM\Column(type: 'integer', length: 256, unique: false)]
    public int $duration = 0;

    public function __construct()
    {
        $this->doctors = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function setDoctors(Collection $doctors): self
    {
        $this->doctors = $doctors;

        return $this;
    }

    public function addDoctor(User $doctor): self
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors[] = $doctor;
            $doctor->addService($this);
        }

        return $this;
    }

    public function removeDoctor(User $doctor): self
    {
        if ($this->doctors->removeElement($doctor)) {
            $doctor->removeService($this);
        }

        return $this;
    }
}