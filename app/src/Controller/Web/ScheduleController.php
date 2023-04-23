<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Form\Web\AddScheduleType;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route(path: '/medic/schedules')]
class ScheduleController extends AbstractController
{
    public function __construct(
        private ScheduleRepository $scheduleRepository,
        private UserRepository $userRepository,
        private TagAwareCacheInterface $cache
    ) {
    }

    #[Route(path: '/add', name: 'web_add_schedule', methods: ['GET', 'POST'])]
    public function addSchedule(Request $request): Response
    {
        $form = $this->createForm(AddScheduleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule = $form->getData();
            $schedule->setDoctor($this->userRepository->findOneBy(['id' => $this->getUser()->getId()]));
            if ($this->scheduleRepository->findBy([
                'doctor' => $this->getUser(),
                'startDate' => $schedule->getStartDate(),
                'endDate' => $schedule->getEndDate()
            ])) {
                $this->addFlash('error', 'You already have a schedule for this dates');

                return $this->redirectToRoute('web_show_schedules');
            }

            $this->scheduleRepository->save($schedule);
            $this->cache->invalidateTags(['schedules_medic_id_' . $this->getUser()->getId()]);

            return $this->redirectToRoute('web_show_schedules');
        }

        return $this->render('web/schedule/add_schedule.html.twig', [
            'scheduleForm' => $form->createView(),
        ]);
    }

    #[Route(name: 'web_show_schedules', methods: ['GET'])]
    public function showSchedules(): Response
    {
        $cacheTag = 'schedules_medic_id_' . $this->getUser()->getId();

        return $this->cache->get($cacheTag, function (ItemInterface $item) use ($cacheTag) {
            $schedules  = $this->scheduleRepository->findBy(['doctor' => $this->getUser()]);
            $item->expiresAfter(43200);

            foreach ($schedules as $schedule) {
                $item->tag('schedule_id_' . $schedule->getId());
            }

            return $this->render('web/schedule/show_schedules.html.twig', [
                'schedules' => $schedules,
            ]);
        });
    }

    #[Route(path: '/{id}', name: 'web_show_schedules_by_id', methods: ['GET'])]
    public function showSchedulesById(int $id): Response
    {
        return $this->cache->get('schedule_id_' . $id, function (ItemInterface $item) use ($id) {
            $schedule = $this->scheduleRepository->findOneBy(['id' => $id]);

            $item->expiresAfter(43200);
            foreach ($schedule->getDays() as $day) {
                $item->tag('day_id_' . $day->getId());
            }
            $item->tag('schedule_id_' . $id);

            return $this->render('web/schedule/show_schedules_by_id.html.twig', [
                'schedule' => $schedule,
                'daysNumber' => $this->scheduleRepository->findOneBy(['id' => $id])->getDays()->count(),
                'possibleDaysNumber' => $this->scheduleRepository->getPossibleDaysNumber($id)
            ]);
        });
    }

    #[Route(path: '/{id}/delete', name: 'web_delete_schedule', methods: ['GET', 'POST'])]
    public function delete(int $id): Response
    {
        $schedule = $this->scheduleRepository->findOneBy(['id' => $id]);
        if (null === $schedule) {
            $this->addFlash('error','Schedule not found');

            return $this->redirectToRoute('web_show_schedules');
        }

        $this->scheduleRepository->delete($schedule);
        $this->cache->invalidateTags(['schedule_id' . $id]);
        $this->addFlash('success','Schedule deleted successfully');

        return $this->redirectToRoute('web_show_schedules');
    }
}