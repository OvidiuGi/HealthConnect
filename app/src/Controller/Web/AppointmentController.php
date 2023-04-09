<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Form\Web\AddAppointmentType;
use App\Message\NewAppointmentNotification;
use App\Repository\AppointmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractController
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private UserRepository $userRepository,
        private MessageBusInterface $bus
    ) {
    }

    #[Route(path:'/appointments', name: 'web_show_appointments', methods: ['GET'])]
    public function showAppointments(Request $request): Response
    {
        $paginate['page'] = (int)$request->query->get('page',1);
        $paginate['size'] = (int)$request->query->get('size',10);

        $appointments = $this
            ->appointmentRepository
            ->getPaginatedByUser($paginate['page'], $paginate['size'], $this->getUser()->getId());
        $totalPages = \ceil(\count($this->appointmentRepository->findBy(['customer' => $this->getUser()])) / $paginate['size']);

        return $this->render('web/appointment/show_appointments_page.html.twig', [
            'appointments' => $appointments,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    #[Route(path: '/medic/{id}/new-appointment', name: 'web_add_appointment_by_medic', methods: ['GET', 'POST'])]
    public function createAppointmentByDoctorId(Request $request): Response
    {
        $form = $this->createForm(AddAppointmentType::class);
        $form->handleRequest($request);
        $id = $request->get('id');
        if ($form->isSubmitted() && $form->isValid()) {
            $appointment = $form->getData();
            $appointment->setCustomer($this->getUser());
            $appointment->setDoctor($this->userRepository->findOneBy(['id' => $id, 'role' => 'ROLE_MEDIC']));
            $appointment->setDate($form->get('date')->getData());
            $this->appointmentRepository->save($appointment);

            // Dispatch confirmation email
            $this->bus->dispatch(new NewAppointmentNotification($appointment));
            return $this->redirectToRoute('web_show_appointments');
        }

        return $this->render('web/appointment/new_appointment.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/appointments/delete/{id}', name: 'web_delete_appointment', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);
        if (null === $appointment) {
            $this->addFlash('error','Appointment not found');

            return $this->redirectToRoute('web_show_appointments');
        }

        $this->appointmentRepository->delete($appointment);
        $this->addFlash('success','Appointment deleted successfully');

        return $this->redirectToRoute('web_medic_appointments');
    }
}