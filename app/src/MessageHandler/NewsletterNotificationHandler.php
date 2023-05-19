<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NewsletterNotification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NewsletterNotificationHandler
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(NewsletterNotification $notification): void
    {
        $email = (new Email())
            ->from('newsletter@healthconnect.com')
            ->to($notification->getUserEmail())
            ->subject($notification->getSubject())
            ->text($notification->getContent());

        $this->mailer->send($email);
    }
}
