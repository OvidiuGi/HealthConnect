<?php

namespace App\Message;

use App\Entity\Appointment;

class NewAppointmentNotification
{
    public function __construct(private Appointment $appointment)
    {
    }

    public function getAppointment(): Appointment
    {
        return $this->appointment;
    }

    public function getCustomerEmail(): string
    {
        return $this->appointment->getCustomer()->email;
    }

    public function getAppointmentDate(): string
    {
        return $this->appointment->getDate()->format('Ymd');
    }

    public function getAppointmentTime(): string
    {
        $timeInterval = explode('-', $this->appointment->timeInterval)[0];

        $timeInterval = str_replace(':', '', $timeInterval);
        return $timeInterval . '00';
    }

    public function getService(): string
    {
        return $this->appointment->getServices()->name;
    }

    public function getLocation(): string
    {
        return $this->appointment->getDoctor()->getOffice()->address;
    }

    public function getDuration(): string
    {
        return $this->appointment->getServices()->duration;
    }
}