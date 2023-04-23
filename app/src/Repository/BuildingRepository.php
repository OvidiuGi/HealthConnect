<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Building;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class BuildingRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Building::class);
    }

    public function getPaginatedFilteredSorted(array $options): array
    {
        $direction = isset($options['direction']) ? strtoupper($options['direction']) : 'ASC';

        if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $options['direction'] = 'ASC';
        }

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('b')
            ->from('App\Entity\Building', 'b')
            ->groupBy('b.id')
            ->setFirstResult(($options['page'] * $options['limit']) - $options['limit'])
            ->setMaxResults($options['limit']);

        if (isset($options['search'])) {
            $query->andWhere('b.name LIKE :search')->setParameter(':search', '%' .$options['search'] . '%');
        }

        if (isset($options['sortBy'])) {
            $query->orderBy('b.' . $options['sortBy'], $direction);
        }

        return $query->getQuery()->execute();
    }

    public function save(Building $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}