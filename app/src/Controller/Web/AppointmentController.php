<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Entity\Appointment;
use App\Form\Web\AddAppointmentType;
use App\Message\NewAppointmentNotification;
use App\Repository\AppointmentRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentRepository  $appointmentRepository,
        private readonly UserRepository         $userRepository,
        private readonly MessageBusInterface    $bus,
        private readonly LoggerInterface        $analyticsLogger,
        private readonly TagAwareCacheInterface $cache,
    ) {
    }

    #[Route(path:'/appointments', name: 'web_show_appointments', methods: ['GET'])]
    public function showAppointments(Request $request): Response
    {
        $page = (int)$request->query->get('page', 1);
        $size = (int)$request->query->get('size', 10);
        $cacheTag = 'show_appointments_' . $this->getUser()->getId() . 'page_' . $page;

        return $this->cache->get($cacheTag, function (ItemInterface $item) use ($page, $size, $cacheTag) {
            $appointments = $this
                ->appointmentRepository
                ->getPaginatedByUser($page, $size, $this->getUser()->getId());
            $totalPages = \ceil(
                \count($this->appointmentRepository->findBy(['customer' => $this->getUser()])) / $size
            );

            $item->expiresAfter(43200);
            $identities = ['show_appointments_' . $this->getUser()->getId()];

            foreach ($appointments as $appointment) {
                $identities[] = 'appointment_id_' . $appointment->getId();
                $identities[] = 'appointment_medic_id_' . $appointment->getMedic()->getId();
                $identities[] = 'appointment_customer_id_' . $appointment->getCustomer()->getId();
                $identities[] = 'appointment_service_id_' . $appointment->getService()->getId();
                $identities[] = 'appointment_is_completed_' . $appointment->getId();
            }
            $item->tag($identities);

            return $this->render('web/appointment/show_appointments_page.html.twig', [
                'appointments' => $appointments,
                'page' => $page,
                'size' => $size,
                'totalPages' => $totalPages,
            ]);
        });
    }

    #[Route(path: '/medic/{id}/new-appointment', name: 'web_add_appointment_by_medic', methods: ['GET', 'POST'])]
    public function createAppointmentByMedicId(Request $request): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AddAppointmentType::class, $appointment);
        $form->handleRequest($request);
        $id = $request->get('id');
        if ($form->isSubmitted() && $form->isValid()) {
            $appointment = $form->getData();
            $appointment->setCustomer($this->getUser());
            $appointment->setMedic($this->userRepository->findOneBy(['id' => $id, 'role' => 'ROLE_MEDIC']));
            $appointment->setEndTime(
                $appointment->getStartTime()->modify('+' . $appointment->getService()->duration . ' minutes')
            );
            $this->appointmentRepository->save($appointment);
            $this->cache->invalidateTags([
                    'show_appointments_' . $this->getUser()->getId(),
                    'show_medic_appointments_' . $appointment->getMedic()->getId(),
            ]);
            $this->analyticsLogger->info(
                'New Appointment Created',
                [
                    'appointmentId' => $appointment->getId(),
                    'medicId' => $appointment->getMedic()->getId(),
                    'customerId' => $appointment->getCustomer()->getId(),
                    'service' => $appointment->getService()->name,
                    'type' => 'new-appointment',
                    'success' => true,
                    'date' => $appointment->getStartTime()->format('d/m/Y'),

                ]
            );

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
            return $this->redirectToRoute('web_show_appointments');
        }
        $this->cache->invalidateTags([
            'appointment_id_' . $appointment->getId()
        ]);
        $this->appointmentRepository->delete($appointment);

        return $this->redirectToRoute('web_show_appointments');
    }
}
