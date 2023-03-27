<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Day;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DayRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Day::class);
    }

    public function getDatesByDoctorId(int $doctorId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('d')
            ->from('App\Entity\Day', 'd')
            ->join('App\Entity\Schedule', 's')
            ->where('d.schedule = s.id')
            ->andWhere('s.doctor = :doctorId')
            ->setParameter('doctorId', $doctorId)
            ->getQuery()
            ->execute();
    }

    public function save(Day $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function delete(Day $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}