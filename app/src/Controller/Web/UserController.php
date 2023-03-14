<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Form\Web\UpdateUserType;
use App\Repository\AppointmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private AppointmentRepository $appointmentRepository;

    public function __construct(UserRepository $userRepository, AppointmentRepository $appointmentRepository)
    {
        $this->userRepository = $userRepository;
        $this->appointmentRepository = $appointmentRepository;
    }

    #[Route(path: '/users', name: 'web_edit_user', methods: ['GET', 'POST'])]
    public function updateUser(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userRepository->update($user);

            return $this->redirectToRoute('web_login');
        }

        return $this->render('web/user/edit_user.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/medics/{id}', name: 'web_show_medics', methods: ['GET'])]
    public function getMedicsByHospitalId(int $id): Response
    {
        return $this->render('web/user/show_medics.html.twig', [
            'medics' => $this->userRepository->findBy(['office' => $id]),
        ]);
    }
}