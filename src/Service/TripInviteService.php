<?php

namespace App\Service;

use App\Entity\TripInvite;
use App\Entity\TripParticipant;
use App\Enum\InviteStatus;
use App\Enum\TripStatus;
use App\Repository\TripInviteRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TripInviteService
{
    public function __construct(
        public TripRepository         $tripRepository,
        public SerializerInterface    $serializer,
        public EntityManagerInterface $manager,
        public UserRepository         $userRepository,
        public TripInviteRepository   $tripInviteRepository
    )
    {}

    /**
     * Accept a trip invite.
     * @param TripInvite $invite
     * @return void
     */
    public function acceptInvite(TripInvite $invite):string
    {
        // si statut decliné encore possible de changer le statut ?

        $invite->setStatus(InviteStatus::ACCEPTED);
        $trip = $this->tripRepository->find($invite->getTrip());

        $participant = new TripParticipant();
        $participant->setTrip($trip);
        $participant->setRole($invite->getRole());
        $participant->setParticipant($invite->getRecipient());
        $this->manager->persist($participant);

        $trip->addParticipant($participant);
        $this->manager->persist($trip);

        $this->manager->flush();

        return "invite accepted";
    }

    public function declineInvite(TripInvite $invite):string
    {
        $invite->setStatus(InviteStatus::DECLINED);
        $this->manager->persist($invite);
        $this->manager->flush();

        return "invite declined";
    }

}