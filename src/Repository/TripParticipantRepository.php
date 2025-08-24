<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\TripParticipant;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripParticipant>
 */
class TripParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripParticipant::class);
    }

    /**
     * @return TripParticipant[] Returns an array of TripParticipant objects
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.participant = :u')
            ->setParameter('u', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneParticipant(User $user, Trip $trip): ?TripParticipant
    {
        return $this->createQueryBuilder('tp')
            ->andWhere('tp.participant = :user')
            ->andWhere('tp.trip = :trip')
            ->setParameter('user', $user)
            ->setParameter('trip', $trip)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
