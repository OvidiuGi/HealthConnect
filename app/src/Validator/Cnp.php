<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Cnp extends Constraint
{
    public string $message = "This is not a valid CNP";
}