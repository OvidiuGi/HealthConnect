<?php

namespace App\MessageHandler;

use App\Message\NewAppointmentNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class NewAppointmentNotificationHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(NewAppointmentNotification $notification)
    {
        $file = fopen('appointment.ics', 'w');

        //create .ics file
        $ical = "BEGIN:VCALENDAR\nPRODID:-//Sales//Appointment//EN\nVERSION:2.0\nBEGIN:VEVENT\nUID:" . Uuid::v4(). "@sales.com\nDTSTART;TZID=Europe/Bucharest:" . $notification->getAppointmentDate() . "T" . $notification->getAppointmentTime() . "\nDURATION:PT" . $notification->getDuration() . "M\nSUMMARY:Appointment reminder\nDESCRIPTION: Service: ". $notification->getService()."\nLOCATION:". $notification->getLocation()."\nEND:VEVENT\nEND:VCALENDAR";
        $ical = trim($ical, ' ');
        fwrite($file, $ical);
        fclose($file);

        $email = (new Email())
            ->from('licenta@sales.com')
            ->to($notification->getCustomerEmail())
            ->subject('New Appointment Confirmation Email')
            ->text('Your appointment has been confirmed! Please find the attached .ics file with the date of the appointment.')
            ->attach(fopen('appointment.ics', 'r'), 'appointment.ics', 'text/calendar');

        $this->mailer->send($email);
    }
}