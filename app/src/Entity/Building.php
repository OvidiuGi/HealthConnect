<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: BuildingRepository::class)]
#[Table(name: '`building`')]
class Building
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    public string $address = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $city = '';

    // One Building has Many Doctors.
    #[ORM\OneToMany(mappedBy: 'office', targetEntity: User::class)]
    private Collection $doctors;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startHour;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endHour;

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $name = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $postalCode = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $phone = '';

    #[ORM\Column(type: 'string', length: 256, unique: true)]
    public string $email = '';

    #[ORM\Column(type: 'string', length: 256, unique: false)]
    public string $description = '';

    public function __construct()
    {
        $this->doctors = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setStartHour(?\DateTimeImmutable $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getStartHour(): ?\DateTimeImmutable
    {
        return $this->startHour;
    }

    public function setEndHour(?\DateTimeImmutable $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeImmutable
    {
        return $this->endHour;
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
            $doctor->setOffice($this);
        }

        return $this;
    }

    public function removeDoctor(User $doctor): self
    {
        if ($this->doctors->removeElement($doctor)) {
            // set the owning side to null (unless already changed)
            if ($doctor->getOffice() === $this) {
                $doctor->setOffice(null);
            }
        }

        return $this;
    }
}
