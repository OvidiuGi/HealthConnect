<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use App\Validator as MyAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: HospitalRepository::class)]
#[Table(name: '`hospital`')]
class Hospital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: true, nullable: false)]
    #[Assert\NotBlank]
    public string $address = '';

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    #[Assert\NotBlank]
    public string $city = '';

    // One Hospital has Many Medics.
    #[ORM\OneToMany(mappedBy: 'office', targetEntity: User::class)]
    private Collection $medics;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private ?\DateTimeImmutable $startHour;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private ?\DateTimeImmutable $endHour;

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    #[Assert\NotBlank]
    public string $name = '';

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    #[Assert\NotBlank]
    public string $postalCode = '';

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    #[MyAssert\TelephoneNumber]
    public string $phone = '';

    #[ORM\Column(type: 'string', length: 256, unique: true, nullable: false)]
    #[Assert\Email]
    public string $email = '';

    #[ORM\Column(type: 'text', unique: false, nullable: false)]
    #[Assert\NotBlank]
    public string $description = '';

    public function __construct()
    {
        $this->medics = new ArrayCollection();
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

    public function getMedics(): Collection
    {
        return $this->medics;
    }

    public function setMedics(Collection $medics): self
    {
        $this->medics = $medics;

        return $this;
    }

    public function addMedic(User $medic): self
    {
        if (!$this->medics->contains($medic)) {
            $this->medics[] = $medic;
            $medic->setOffice($this);
        }

        return $this;
    }

    public function removeMedic(User $medic): self
    {
        if ($this->medics->removeElement($medic)) {
            // set the owning side to null (unless already changed)
            if ($medic->getOffice() === $this) {
                $medic->setOffice(null);
            }
        }

        return $this;
    }
}
