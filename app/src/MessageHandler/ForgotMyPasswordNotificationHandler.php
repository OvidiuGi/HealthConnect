<?php

namespace App\MessageHandler;

use App\Message\ForgotMyPasswordNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsMessageHandler]
class ForgotMyPasswordNotificationHandler
{
    public function __construct(private MailerInterface $mailer, private RouterInterface $router)
    {
    }

    public function __invoke(ForgotMyPasswordNotification $notification)
    {
        $changePasswordUrl = $this->router->generate(
            'reset_password',
            ['token' => $notification->getForgotPassowrdToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('licenta@sales.com')
            ->to($notification->getEmail())
            ->subject('Password change request')
            ->text("We've received your password change request.\n This link will expire in 1 hour.")
            ->html("<a href=$changePasswordUrl>Click here to change your password!</a>");

        $this->mailer->send($email);
    }
}