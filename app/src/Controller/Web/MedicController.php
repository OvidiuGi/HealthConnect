<?php

namespace App\Controller\Web;

use App\Form\Web\UpdateMedicType;
use App\Repository\AppointmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicController extends AbstractController
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private UserRepository $userRepository
    ) {
    }
    #[Route(path: '/medic/appointments', name: 'web_medic_appointments', methods: ['GET'])]
    public function showAppointmentsByMedic(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page',1);
        $paginate['size'] = (int)$request->query->get('size',10);

        $appointments = $this
            ->appointmentRepository
            ->getPaginatedByMedic($paginate['page'], $paginate['size'], $this->getUser()->getId());
        $totalPages = \ceil(\count($this->appointmentRepository->findAll()) / $paginate['size']);

        return $this->render('web/appointment/show_medic_appointments_page.html.twig', [
            'appointments' => $appointments,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    #[Route(path: '/medic/edit', name: 'web_edit_medic', methods: ['GET', 'POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateMedicType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->userRepository->update($user);

            return $this->redirectToRoute('web_main_page');
        }

        return $this->render('web/user/edit_medic.html.twig', ['form' => $form->createView()]);
    }
}