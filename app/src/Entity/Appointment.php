<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ORM\Table(name: 'appointments')]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[Assert\NotBlank(message: 'Please select the start time')]
    private ?\DateTimeImmutable $startTime;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private ?\DateTimeImmutable $endTime;

    // Many Appointment have one Customer.
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $customer;

    // Many Services have one Appointment.
    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id')]
    public ?Service $service;

    // Many Appointment have one Medic.
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'medic_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $medic;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[Assert\NotBlank(message: 'Please select the date')]
    private ?\DateTimeImmutable $date;

    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $isCompleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): User
    {
        return $this->customer;
    }

    public function setCustomer(User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service ?? null;
    }

    public function setServices(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getMedic(): ?User
    {
        return $this->medic ?? null;
    }

    public function setMedic(?User $medic): self
    {
        $this->medic = $medic;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date ?? null;
    }

    public function setDate(?\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime ?? null;
    }

    public function setStartTime(?\DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime ?? null;
    }

    public function setEndTime(?\DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }
}
