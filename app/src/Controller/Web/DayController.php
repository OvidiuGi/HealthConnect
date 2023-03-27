<?php

namespace App\Controller\Web;

use App\Form\Web\AddDayType;
use App\Repository\DayRepository;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/day')]
class DayController extends AbstractController
{
    private DayRepository $dayRepository;

    private ScheduleRepository $scheduleRepository;

    public function __construct(DayRepository $dayRepository, ScheduleRepository $scheduleRepository)
    {
        $this->dayRepository = $dayRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    #[Route(path: '/add/{id}', name: 'web_add_day', methods: ['GET', 'POST'])]
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
    public function delete(int $id): Response
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
}