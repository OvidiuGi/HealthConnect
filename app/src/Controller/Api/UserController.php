<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/users')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    private RoleRepository $roleRepository;

    private UserRepository $userRepository;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    #[Route(name: 'api_register_user', methods: ['POST'])]
    public function register(UserDto $userDto): JsonResponse
    {
        $userDto->setRole($this->roleRepository->findOneBy(['id' => $userDto->roleId]));
        $user = User::createFromUserDto($userDto);
        $user->password = $this->passwordHasher->hashPassword($user, $user->plainPassword);

        $this->userRepository->save($user);

        return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    }
}