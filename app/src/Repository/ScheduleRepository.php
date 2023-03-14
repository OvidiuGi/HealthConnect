<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Schedule::class);
    }

    public function save(Schedule $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function getDoctorDays(int $doctorId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('d.date')
            ->from('App\Entity\Day', 'd')
            ->join('App\Entity\Schedule', 's')
            ->where('d.schedule = s.id')
            ->andWhere('s.doctor = :doctorId')
            ->setParameter('doctorId', $doctorId)
            ->getQuery()
            ->execute();
    }

    public function getAvailableDates(int $scheduleId): array
    {
        $schedule = $this->findOneBy(['id' => $scheduleId]);

        $result = [];
        $currentDay = $schedule->getStartDate();

        while ($currentDay <= $schedule->getEndDate()) {
            foreach ($schedule->getDays() as $day) {
                if ($day->getDate()->format('Y-m-d') !== $schedule->getStartDate()->format('Y-m-d')) {
                    $result[] = $schedule->getStartDate();
                }
            }
            if ($schedule->getDays()->count() === 0) {
                $result[] = $currentDay;
            }

            $currentDay = $currentDay->modify('+1 day');
//            $schedule->setStartDate($schedule->getStartDate()->modify('+1 day'));
        }

        return $result;
    }
}
