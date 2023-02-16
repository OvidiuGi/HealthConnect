<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\User;

class UserDto
{
    public int $id;

    public string $firstName;

    public string $lastName;

    public string $email;

    public string $password;

    public string $telephoneNr;

    public string $cnp;

    public string $role;

    public static function createFromUser(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->firstName = $user->firstName;
        $dto->lastName = $user->lastName;
        $dto->email = $user->email;
//        $dto->password = $user->password;
        $dto->telephoneNr = $user->telephoneNr;
        $dto->cnp = $user->cnp;
        $dto->role = $user->role;

        return $dto;
    }
}