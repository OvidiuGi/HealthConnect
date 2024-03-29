<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Admin\UpdateMedicType;
use App\Form\Admin\UpdateUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/admin')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TagAwareCacheInterface $cache
    ) {
    }

    #[Route(path: "/users", name: 'admin_show_users', methods: ['GET'])]
    public function showUsers(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page', 1);
        $paginate['size'] = (int)$request->query->get('size', 10);

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
        $paginate['page'] = (int)$request->query->get('page', 1);
        $paginate['size'] = (int)$request->query->get('size', 10);

        $users = $this->userRepository->getPaginatedMedics($paginate['page'], $paginate['size']);
        $totalPages = \ceil(\count($this->userRepository->findAll()) / $paginate['size']);

        return $this->render('admin/user/show_medics_page.html.twig', [
            'users' => $users,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    #[Route(path: "/users/{id}", name: "admin_edit_user", methods: ["GET", "POST"])]
    public function editUser(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userRepository->update($user);

            return $this->redirectToRoute('admin_show_users');
        }

        return $this->render('admin/user/edit_user.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: "/medics/{id}", name: "admin_edit_medic", methods: ["GET", "POST"])]
    public function editMedic(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);

        $form = $this->createForm(UpdateMedicType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userRepository->update($user);
            $this->cache->invalidateTags([
                'appointment_medic_id_' . $user->getId(),
                'browse_medics_hospital_' . $user->getOffice()->getId(),
            ]);

            return $this->redirectToRoute('admin_show_medics');
        }

        return $this->render('admin/user/edit_medic.html.twig', ['form' => $form->createView()]);
    }
}
