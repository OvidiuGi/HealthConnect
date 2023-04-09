<?php

namespace App\Controller\Web;

use App\Entity\Appointment;
use App\Form\Web\AddBreakType;
use App\Form\Web\AddDayType;
use App\Repository\AppointmentRepository;
use App\Repository\DayRepository;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/medic/days')]
class DayController extends AbstractController
{
    public function __construct(
        private DayRepository $dayRepository,
        private ScheduleRepository $scheduleRepository,
        private AppointmentRepository $appointmentRepository,
    ) {
    }

    #[Route(path: '/schedule/{id}/add', name: 'web_add_day', methods: ['GET', 'POST'])]
    public function add(int $id, Request $request): Response
    {
        $form = $this->createForm(AddDayType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $day = $form->getData();
            $date = $day->getDate();
            $endTime = $day->getEndTime();
            $startTime = $day->getStartTime();
            $endTime = $endTime->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $startTime = $startTime->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
            $day->setEndTime($endTime);
            $day->setStartTime($startTime);

            $day->setSchedule($this->scheduleRepository->findOneBy(['id' => $id]));

            $this->dayRepository->save($day);

            return $this->redirectToRoute('web_show_schedules');
        }

        return $this->render('web/day/add_day.html.twig', [
            'dayForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'web_delete_day', methods: ['GET', 'POST'])]
    public function deleteDay(int $id): Response
    {
        $day = $this->dayRepository->findOneBy(['id' => $id]);
        if (null === $day) {
            $this->addFlash('error','Day not found');

            return $this->redirectToRoute('web_show_schedules');
        }

        $this->dayRepository->delete($day);
        $this->addFlash('success','Day deleted successfully');

        return $this->redirectToRoute('web_show_schedules');
    }

    #[Route(path: '/{id}/add-break', name: 'web_add_break', methods: ['GET', 'POST'])]
    public function addBreak(int $id, Request $request): Response
    {
        $day = $this->dayRepository->findOneBy(['id' => $id]);
        $appointment = new Appointment();
        $appointment->setDoctor($this->getUser());
        $appointment->setCustomer($this->getUser());
        $appointment->setDate($day->getDate());

        $form = $this->createForm(AddBreakType::class, $appointment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment = $form->getData();

            $this->appointmentRepository->save($appointment);

            return $this->redirectToRoute('web_show_schedules');
        }

        return $this->render('web/day/add_break.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/show-breaks', name: 'web_show_breaks', methods: ['GET'])]
    public function showBreaks(int $id): Response
    {
        $day = $this->dayRepository->findOneBy(['id' => $id]);

        return $this->render('web/day/show_breaks.html.twig', [
            'breaks' => $this->appointmentRepository->findBy(['date' => $day->getDate(), 'service' => null]),
            'date' => $day->getDate()->format('d-m-Y'),
        ]);
    }

    #[Route(path: '/delete-break/{id}', name: 'web_delete_break', methods: ['POST'])]
    public function deleteBreak(int $id): Response
    {
        $appointment = $this->appointmentRepository->findOneBy(['id' => $id]);
        if (null === $appointment) {
            $this->addFlash('error','Break not found');

            return $this->redirectToRoute('web_show_schedules');
        }

        $this->appointmentRepository->delete($appointment);
        $this->addFlash('success','Break deleted successfully');

        return $this->redirectToRoute('web_show_schedules');
    }
}