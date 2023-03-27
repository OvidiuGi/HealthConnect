<?php

namespace App\MessageHandler;

use App\Message\NewsletterNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NewsletterNotificationHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(NewsletterNotification $notification)
    {
        $email = (new Email())
            ->from('licenta@newsletter.com')
            ->to($notification->getUserEmail())
            ->subject($notification->getSubject())
            ->text($notification->getContent());

        $this->mailer->send($email);
    }
}