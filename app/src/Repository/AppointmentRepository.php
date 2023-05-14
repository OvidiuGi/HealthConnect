<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Appointment::class);
    }

    public function getPaginatedByUser(int $page, int $limit, int $userId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Appointment', 'a')
            ->where('a.customer = :userId')
            ->andWhere('a.service IS NOT NULL')
            ->groupBy('a.id')
            ->setParameter('userId', $userId)
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function getPaginated(int $page, int $limit): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Appointment', 'a')
            ->where('a.service IS NOT NULL')
            ->groupBy('a.id')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function save(Appointment $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function delete(Appointment $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function getPaginatedByMedic(int $page, int $limit, int $medicId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('a')
            ->from('App\Entity\Appointment', 'a')
            ->where('a.medic = :medicId')
            ->andWhere('a.service IS NOT NULL')
            ->groupBy('a.id')
            ->setParameter('medicId', $medicId)
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function getAppointmentsIntervalByDate(\DateTimeImmutable $date): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('a.startTime, a.endTime')
            ->from('App\Entity\Appointment', 'a')
            ->where('a.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }
}
