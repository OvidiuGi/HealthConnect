<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Password extends Constraint
{
    public string $message = 'Password not valid!';
}