<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class HospitalRepository extends ServiceEntityRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Hospital::class);
    }

    public function getPaginatedFilteredSorted(array $options): array
    {
        $direction = isset($options['direction']) ? strtoupper($options['direction']) : 'ASC';

        if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $options['direction'] = 'ASC';
        }

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('h')
            ->from('App\Entity\Hospital', 'h')
            ->groupBy('h.id')
            ->setFirstResult(($options['page'] * $options['limit']) - $options['limit'])
            ->setMaxResults($options['limit']);

        if (isset($options['search'])) {
            $query->andWhere('h.name LIKE :search')->setParameter(':search', '%' . $options['search'] . '%');
        }

        if (isset($options['sortBy'])) {
            $query->orderBy('h.' . $options['sortBy'], $direction);
        }

        return $query->getQuery()->execute();
    }

    public function save(Hospital $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
