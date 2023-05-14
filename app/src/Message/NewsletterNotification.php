<?php

namespace App\Message;

use App\Entity\User;

class NewsletterNotification
{
    public function __construct(private User $user, private string $content, private string $subject)
    {
    }

    public function getUserEmail(): string
    {
        return $this->user->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
