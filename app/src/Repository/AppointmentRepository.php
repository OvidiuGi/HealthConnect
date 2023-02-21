<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class AppointmentRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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
            ->groupBy('a.id')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO: Implement upgradePassword() method.
    }
}