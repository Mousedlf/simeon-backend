<?php

namespace App\Service;

use App\Repository\TripInviteRepository;
use App\Repository\TripParticipantRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ItineraryService
{
    public function __construct(
        public TripRepository            $tripRepository,
        public SerializerInterface       $serializer,
        public EntityManagerInterface    $manager,
        public UserRepository            $userRepository,
        public TripInviteRepository      $tripInviteRepository,
        public TripParticipantRepository $tripParticipantRepository,
    )
    {
    }


}