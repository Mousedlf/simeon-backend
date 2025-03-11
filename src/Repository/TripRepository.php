<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * @return Trip[] Returns an array of Trip objects
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByName($name): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByNameAndUser($name, $user): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->andWhere('t.owner = :user')
            ->setParameter('name', $name)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }


}
