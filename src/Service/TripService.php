<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class TripService
{
    public function __construct(
        public TripRepository $tripRepository,
        public SerializerInterface $serializer,
        public EntityManagerInterface $manager
    ){}

    /**
     * Get all trips of a user.
     * @param User $user
     * @return array
     */
    public function getTripsOfUser(User $user): array
    {
        return $this->tripRepository->findByUser($user);
    }

    /**
     * Create a new trip.
     * @param $user
     * @param Request $request
     * @return Trip|string
     */
    public function createTrip($user, Request $request): Trip|string //preciser type de User
    {
        $trip = $this->serializer->deserialize($request->getContent(), Trip::class, 'json');
        $trip->setOwner($user);
        //$trip->setDays();

        if($trip->getStartDate() > $trip->getEndDate()) {
            return "start date must be after end date";
        }

        $existingTrip = $this->tripRepository->findOneByName($trip->getName());
        if($existingTrip) {
            return "trip by this name already exists";
        }

        $this->manager->persist($trip);
        $this->manager->flush();

        return $trip;
    }

    /**
     * Edit textual infos of a trip (title, description).
     * @param Trip $trip
     * @param Request $request
     * @return Trip|string
     */
    public function editTripNameAndDescription(Trip $trip, Request $request): Trip|string
    {
        $editedTrip = $this->serializer->deserialize($request->getContent(), Trip::class, 'json');

        $existingTrip = $this->tripRepository->findOneByName($editedTrip->getName());
        if($existingTrip && $existingTrip->getId() !== $trip->getId()) {
            return "trip by this name already exists";
        }

        $trip->setName($editedTrip->getName());
        $trip->setDescription($editedTrip->getDescription());

        $this->manager->persist($trip);
        $this->manager->flush();

        return $trip;
    }

    /**
     * Delete a trip.
     * @param Trip $trip
     * @return string
     */
    public function deleteTrip(Trip $trip): string
    {
        $this->manager->remove($trip);
        $this->manager->flush();

        return "trip successfully deleted";
    }
}