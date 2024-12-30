<?php

namespace App\Repository;

use App\Entity\TripInvite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripInvite>
 */
class TripInviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripInvite::class);
    }

    //    /**
    //     * @return TripInvite[] Returns an array of TripInvite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

        public function findOneByRecipientAndTrip($recipient, $trip): ?TripInvite
        {
            return $this->createQueryBuilder('i')
                ->andWhere('i.recipient = :r')
                ->andWhere('i.trip = :t')
                ->setParameter('r', $recipient)
                ->setParameter('t', $trip)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
