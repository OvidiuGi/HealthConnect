<?php

namespace App\Controller\Admin;

use App\Form\Admin\UpdateAppointmentType;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
class AppointmentController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    #[Route(path: "/appointments", name: 'admin_show_appointments', methods: ['GET'])]
    public function showAppointments(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page',1);
        $paginate['size'] = (int)$request->query->get('size',10);

        $appointments = $this->appointmentRepository->getPaginated($paginate['page'], $paginate['size']);
        $totalPages = \ceil(\count($this->appointmentRepository->findAll()) / $paginate['size']);

        return $this->render('admin/appointment/show_appointments_page.html.twig', [
            'appointments' => $appointments,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    #[Route(path: "/appointments/{id}", name: 'admin_edit_appointment', methods: ['GET', 'POST'])]
    public function editAppointment(int $id, Request $request): Response
    {
        $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(UpdateAppointmentType::class, $appointment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->appointmentRepository->save($appointment);

            return $this->redirectToRoute('admin_show_appointments');
        }

        return $this->render('admin/appointment/edit_appointments.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}