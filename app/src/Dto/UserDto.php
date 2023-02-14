<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;

class UserDto
{
    public int $id = 0;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $password = '';

    public string $telephoneNr = '';

    public string $cnp = '';

    public int $roleId = 0;

    private ?Role $role = null;

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

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
        $dto->roleId = $user->getRole()->getId();

        return $dto;
    }
}