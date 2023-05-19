<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NewAppointmentNotification;
use App\Service\CalendarFileFactory;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NewAppointmentNotificationHandler
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(NewAppointmentNotification $notification): void
    {
        $reminder = CalendarFileFactory::createCalendarFile($notification);

        $email = (new Email())
            ->from('sales@healthconnect.com')
            ->to($notification->getCustomerEmail())
            ->subject('New Appointment Confirmation Email')
            ->text(
                'Your appointment has been confirmed!
                Please find the attached .ics file with the date of the appointment.'
            )
            ->attach($reminder, 'reminder.ics', 'text/calendar');

        $this->mailer->send($email);

        unlink('reminder.ics');
    }
}
