<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route(path: "/users", name: 'admin_show_users', methods: ['GET'])]
    public function showUsers(Request $request): Response
    {
        $paginate['page'] = $request->query->get('page',1);
        $paginate['size'] = $request->query->get('size',10);

        $users = $this->userRepository->getPaginated($paginate['page'], $paginate['size']);
        $totalPages = \ceil(\count($this->userRepository->findAll()) / $paginate['size']);

        return $this->render('admin/user/show_users_page.html.twig', [
            'users' => $users,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    #[Route(path: "/medics", name: "admin_show_medics", methods: ["GET"])]
    public function showMedics(Request $request): Response
    {
        $paginate['page'] = $request->query->get('page',1);
        $paginate['size'] = $request->query->get('size',10);

        $users = $this->userRepository->getPaginatedMedics($paginate['page'], $paginate['size']);
        $totalPages = \ceil(\count($this->userRepository->findAll()) / $paginate['size']);

        return $this->render('admin/user/show_medics_page.html.twig', [
            'users' => $users,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }
}