<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TelephoneNumberValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof TelephoneNumber) {
            throw new UnexpectedTypeException($constraint, TelephoneNumber::class);
        }

        if (\count(\explode(' ', $value)) > 1) {
            $constraint->message = 'This is not a valid telephone number';
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        if (
            \preg_match(
                '/^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/',
                $value,
                $matches
            )
        ) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
