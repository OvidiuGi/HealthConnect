<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\Web\AddScheduleType;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/schedule')]
class ScheduleController extends AbstractController
{
    private ScheduleRepository $scheduleRepository;

    private UserRepository $userRepository;

    public function __construct(ScheduleRepository $scheduleRepository, UserRepository $userRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->userRepository = $userRepository;
    }

    #[Route(path: '/add', name: 'web_add_schedule', methods: ['GET', 'POST'])]
    public function addSchedule(Request $request): Response
    {
        $form = $this->createForm(AddScheduleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();

            $schedule->setDoctor($this->userRepository->findOneBy(['id' => $this->getUser()->getId()]));

            $this->scheduleRepository->save($schedule);

            return $this->redirectToRoute('web_show_schedules');
        }

        return $this->render('web/schedule/add_schedule.html.twig', [
            'scheduleForm' => $form->createView(),
        ]);
    }

    #[Route(name: 'web_show_schedules', methods: ['GET'])]
    public function showSchedules(): Response
    {
        return $this->render('web/schedule/show_schedules.html.twig', [
            'schedules' => $this->scheduleRepository->findBy(['doctor' => $this->getUser()]),
        ]);
    }

    #[Route(path: '/{id}', name: 'web_show_schedules_by_id', methods: ['GET'])]
    public function showSchedulesById(int $id): Response
    {
        return $this->render('web/schedule/show_schedules_by_id.html.twig', [
            'schedule' => $this->scheduleRepository->findOneBy(['id' => $id]),
        ]);
    }
}