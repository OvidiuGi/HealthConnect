<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TelephoneNumber extends Constraint
{
    public string $message = 'This is not a valid telephone number';
}
