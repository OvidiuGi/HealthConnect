<?php

namespace App\Message;

use App\Entity\Appointment;

class AppointmentReminderNotification
{
    public function __construct(private Appointment $appointment, private int $remainingHours)
    {
    }

    public function getCustomerEmail(): string
    {
        return $this->appointment->getCustomer()->email;
    }

    public function getRemainingHours(): int
    {
        return $this->remainingHours;
    }

    public function getService(): string
    {
        return $this->appointment->getServices()->name;
    }

    public function getDate(): string
    {
        return $this->appointment->getDate()->format('d/m/Y');
    }

    public function getTimeInterval(): string
    {
        return $this->appointment->timeInterval;
    }

    public function getMedicName(): string
    {
        return $this->appointment->getMedic()->firstName . ' ' . $this->appointment->getMedic()->lastName;
    }
}
