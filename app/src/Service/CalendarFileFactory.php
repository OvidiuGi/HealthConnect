<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\NewAppointmentNotification;
use Symfony\Component\Uid\Uuid;

class CalendarFileFactory
{
    public static function createCalendarFile(NewAppointmentNotification $notification)
    {
        $file = fopen('reminder.ics', 'w');
        $uuid = Uuid::v4();

        $ical = "BEGIN:VCALENDAR\n" .
            "PRODID:-//Sales//Appointment//EN\n" .
            "VERSION:2.0\n" .
            "BEGIN:VEVENT\n" .
            "UID:$uuid@sales.com\n" .
            "DTSTART;TZID=Europe/Bucharest:{$notification->getDate()}T{$notification->getStartTime()}\n" .
            "DURATION:PT{$notification->getDuration()}M\n" .
            "SUMMARY:Appointment reminder\n" .
            "DESCRIPTION: Service: {$notification->getService()}\n" .
            "LOCATION:{$notification->getLocation()}\n" .
            "END:VEVENT\n" .
            "END:VCALENDAR"
        ;

        fwrite($file, $ical);
        fclose($file);

        return fopen('reminder.ics', 'r');
    }
}