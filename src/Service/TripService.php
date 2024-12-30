<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\TripInvite;
use App\Entity\TripParticipant;
use App\Entity\User;
use App\Enum\InviteStatus;
use App\Enum\ParticipantStatus;
use App\Repository\TripInviteRepository;
use App\Repository\TripParticipantRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class TripService
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

        $participant = new TripParticipant();
        $participant->setTrip($trip);
        $participant->setParticipant($user);
        $participant->setRole(ParticipantStatus::OWNER);

        $this->manager->persist($participant);
        $trip->addParticipant($participant);

        if ($trip->getStartDate() > $trip->getEndDate()) {
            return "start date must be after end date";
        }

        $existingTrip = $this->tripRepository->findOneByName($trip->getName());
        if ($existingTrip) {
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
        if ($existingTrip && $existingTrip->getId() !== $trip->getId()) {
            return "trip by this name already exists";
        }

        $trip->setName($editedTrip->getName());
        $trip->setDescription($editedTrip->getDescription());

        $this->manager->persist($trip);
        $this->manager->flush();

        return $trip;
    }

    public function editTripDates(Trip $trip, Request $request): Trip|string
    {
        $editedTrip = $this->serializer->deserialize($request->getContent(), Trip::class, 'json');

        if ($editedTrip->getStartDate() > $editedTrip->getEndDate()) {
            return "start date must be after end date";
        }

        $trip->setStartDate($editedTrip->getStartDate());
        $trip->setEndDate($editedTrip->getEndDate());
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

    /**
     * Add participant(s) to a trip.
     * @param Trip $trip
     * @param Request $request
     * @return void
     */
    public function addPeopleToTrip(Trip $trip, Request $request, $currentUser): array|string
    {
        $data = $request->toArray();
        $invitedPeople = [];

        foreach ($data['people'] as $potentialPerson) {

            $potentialUserId = $potentialPerson["potentialUserId"];
            $user = $this->userRepository->findOneByStatusAndId(true, $potentialUserId);
            if ($potentialUserId == $currentUser->getId()) {
                return "can not add yourself";
            } elseif (!$user) {
                return "no public user found with id " . $potentialUserId; // RETURN RESPONSE ?
            }

            $alreadyInvited = $this->tripInviteRepository->findOneByRecipientAndTrip($user, $trip);
            switch ($alreadyInvited->getStatus()) {
                case InviteStatus::ACCEPTED:
                    return "user " . $potentialUserId . " already part of the trip"; // RETURN RESPONSE ?
                case InviteStatus::DECLINED:
                    return "user " . $potentialUserId . " declined invite to this trip";
                case InviteStatus::PENDING:
                    return "user " . $potentialUserId . " already invited to this trip";
            } // --------------------------------------------------- SUPPRESSION des invitations accepted/declined au bout de combien de temps?

            $alreadyParticipating = $this->tripRepository->findOneByName($user, $trip);
            if ($alreadyParticipating) {
                return "user " . $potentialUserId . " already part of the trip"; // RETURN R
            }

            $invite = new TripInvite();
            $invite->setTrip($trip);
            $invite->setSender($currentUser);
            $invite->setRecipient($user);
            $invite->setCreatedAt(new DateTimeImmutable());
            $invite->setMessage($data['message']);

            $role = match ($potentialPerson['statusId']) {
                1 => ParticipantStatus::VIEWER,
                2 => ParticipantStatus::EDITOR,
                default => ParticipantStatus::VIEWER,
            };
            $invite->setRole($role);
            $invite->setStatus(InviteStatus::PENDING);

            $this->manager->persist($invite);
            $invitedPeople[] = $user->getUsername();
        }
        $this->manager->flush();
        return [
            'message' => "trip invites sent successfully to " . count($data['people']) . " people",
            'people' => $invitedPeople
        ];  // RETURN RESPONSE ?
    }

    /**
     * Remove participant(s) of a trip.
     * @param Trip $trip
     * @param Request $request
     * @return void
     */
    public function removePeopleFromTrip(Trip $trip, Request $request): string
    {
        $data = $request->toArray();
        foreach ($data['peopleIds'] as $potentialParticipantId) {
            $participant = $this->tripParticipantRepository->findOneParticipant($potentialParticipantId, $trip);
            switch ($participant):
                case null:
                    return "no participant with id " . $potentialParticipantId . " found";
                case $participant->getRole() == ParticipantStatus::OWNER:
                    return "can not remove yourself. You can only delete the trip ?"; // choisir ce qu'il se passe si admin part
            endswitch;

            $trip->removeParticipant($participant);
            $this->manager->persist($trip);
        }
        $this->manager->flush();
        return "people successfully removed";
    }

    /**
     * Manage roles of participants.
     * @param User $user
     * @param Trip $trip
     * @param Request $request
     * @return void
     */
    public function changeStatusOfParticipant(User $user, Trip $trip, Request $request): void
    {

    }
}