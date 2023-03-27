<?php

namespace App\Message;

use App\Entity\User;

class ForgotMyPasswordNotification
{
    public function __construct(private User $user)
    {
    }

    public function getEmail(): string
    {
        return $this->user->email;
    }

    public function getForgotPassowrdToken(): string
    {
        return $this->user->forgotPasswordToken;
    }
}