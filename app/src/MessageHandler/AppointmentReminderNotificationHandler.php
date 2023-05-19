<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\AppointmentReminderNotification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class AppointmentReminderNotificationHandler
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(AppointmentReminderNotification $notification): void
    {
        $email = (new Email())
            ->from('sales@healthconnect.com')
            ->to($notification->getCustomerEmail())
            ->subject('Appointment reminder!')
            ->text("
            Hello, we want to remind you that the appointment is scheduled in {$notification->getRemainingHours()} hours.\n
            Service: {$notification->getService()}.\n
            Date: {$notification->getDate()}: {$notification->getTimeInterval()}\n
            Medic: {$notification->getMedicName()}\n
            ");

        $this->mailer->send($email);
    }
}
