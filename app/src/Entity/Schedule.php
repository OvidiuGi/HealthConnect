<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\Table(name: 'schedules')]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private int $id;

    // One Doctor has many Schedules.
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: 'doctor_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $doctor;

    // One Schedule has many Days.
    #[ORM\OneToMany(mappedBy: 'schedule', targetEntity: Day::class)]
    private Collection $days;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeImmutable $endDate;

    public function __construct()
    {
        $this->days = new ArrayCollection();
    }

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

    public function getDays(): Collection
    {
        return $this->days;
    }

    public function setDays(Collection $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function addDay(Day $day): self
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
            $day->setSchedule($this);
        }

        return $this;
    }

    public function removeDay(Day $day): self
    {
        if (!$this->days->contains($day)) {
            return $this;
        }

        $this->days->removeElement($day);
        $day->setSchedule(null);

        return $this;
    }
}