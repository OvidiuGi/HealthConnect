<?php

namespace App\Controller\Web;

use App\Analytics\AppointmentsAnalytics;
use App\Analytics\LogParser;
use App\Form\Web\UpdateMedicType;
use App\Repository\AppointmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MedicController extends AbstractController
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private UserRepository $userRepository,
        private LogParser $logParser,
        private AppointmentsAnalytics $appointmentsAnalytics,
        private TagAwareCacheInterface $cache,
    ) {
    }
    #[Route(path: '/medic/appointments', name: 'web_medic_appointments', methods: ['GET'])]
    public function showAppointmentsByMedic(Request $request): Response
    {
        $cacheTag = 'show_medic_appointments_' . $this->getUser()->getId();

        return $this->cache->get($cacheTag, function (ItemInterface $item) use ($request, $cacheTag) {
            $paginate['page'] = (int)$request->query->get('page',1);
            $paginate['size'] = (int)$request->query->get('size',10);

            $appointments = $this
                ->appointmentRepository
                ->getPaginatedByMedic($paginate['page'], $paginate['size'], $this->getUser()->getId());
            $totalPages = \ceil(\count($this->appointmentRepository->findAll()) / $paginate['size']);

            $item->expiresAfter(43200);

            $identities = ['show_medic_appointments_' . $this->getUser()->getId()];

            foreach ($appointments as $appointment) {
                $identities[] = 'appointment_id_' . $appointment->getId();
                $identities[] = 'appointment_doctor_id_' . $appointment->getDoctor()->getId();
                $identities[] = 'appointment_customer_id_' . $appointment->getCustomer()->getId();
                $identities[] = 'appointment_service_id_' . $appointment->getService()->getId();
                $identities[] = 'appointment_is_completed_' . $appointment->getId();
            }
            $item->tag($identities);

            return $this->render('web/appointment/show_medic_appointments_page.html.twig', [
                'appointments' => $appointments,
                'page' => $paginate['page'],
                'size' => $paginate['size'],
                'totalPages' => $totalPages,
            ]);
        });
    }

    #[Route(path: '/medic/edit', name: 'web_edit_medic', methods: ['GET', 'POST'])]
    public function update(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateMedicType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->cache->invalidateTags([
                'appointment_doctor_id_' . $user->getId(),
                'browse_medics_hospital_' . $user->getOffice()->getId(),
            ]);
            $this->userRepository->update($user);

            return $this->redirectToRoute('web_main_page');
        }

        return $this->render('web/user/edit_medic.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/medic/appointments-analytics', name: 'web_medic_appointments_chart_analytics', methods: ['GET'])]
    public function appointmentsAnalytics(): Response
    {
        $analytics = $this->logParser->parseLogs($this->appointmentsAnalytics);

        $appointments = [];

        $analytics->getByMedicId($this->getUser()->getId())->map(function ($item) use (&$appointments,&$analytics) {
            $appointments[$item->context['date']] = $analytics->getAppointmentsByDate($item->context['date']);
        });

        return $this->render('web/analytics/appointments_chart.html.twig', ['appointments' => $appointments]);
    }

    #[Route(path: '/medic/services-analytics', name: 'web_medic_services_chart_analytics', methods: ['GET'])]
    public function servicesAnalytics(): Response
    {
        $analytics = $this->logParser->parseLogs($this->appointmentsAnalytics);

        $services = [];

        $analytics->getByMedicId($this->getUser()->getId())->map(function ($item) use (&$services,&$analytics) {
            $services[$item->context['service']] = $analytics->getAppointmentsByService($item->context['service']);
        });

        return $this->render('web/analytics/services_chart.html.twig', ['services' => $services]);
    }

    #[Route(path: '/medic/appointments/delete/{id}', name: 'web_medic_delete_appointment', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);
        if (null === $appointment) {
            $this->addFlash('error','Appointment not found');

            return $this->redirectToRoute('web_show_appointments');
        }
        $this->cache->invalidateTags([
            'appointment_id_' . $appointment->getId()
        ]);
        $this->appointmentRepository->delete($appointment);
        $this->addFlash('success','Appointment deleted successfully');

        return $this->redirectToRoute('web_medic_appointments');
    }

    #[Route(path: '/medic', name: 'web_medic_main_page', methods: ['GET'])]
    public function mainPage(): Response
    {
        $analytics = $this->logParser->parseLogs($this->appointmentsAnalytics);

        $appointments = [];
        $services = [];

        $analytics->getByMedicId(
                $this->getUser()->getId()
            )
            ->map(function ($item) use (&$appointments,&$analytics, &$services) {
            $appointments[$item->context['date']] = $analytics->getAppointmentsByDate($item->context['date']);
            $services[$item->context['service']] = $analytics->getAppointmentsByService($item->context['service']);
        });


        return $this->render('web/main_page/medic_main_page.html.twig', [
            'appointments' => $appointments,
            'services' => $services,
        ]);
    }
}