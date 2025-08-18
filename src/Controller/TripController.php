<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\ParticipantStatus;
use App\Repository\TripParticipantRepository;
use App\Repository\TripRepository;
use App\Service\TripInviteService;
use App\Service\TripService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/trip')]
class TripController extends AbstractController
{
    /**
     * Get all trips of a user is he is public or if it's the current user.
     * @param User|null $user
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/all/{id}', methods: ['GET'])]
    public function getAllTripsOfUser(?User $user, TripService $tripService): Response
    {
        if (!$user) {
            return $this->json("User not found", Response::HTTP_NOT_FOUND);
        }
        if ($user->isPublic() or $user === $this->getUser()) {
            $trips = $tripService->getTripsOfUser($user);
            return $this->json($trips, Response::HTTP_OK, [], ['groups' => 'trip:read']);
        } else {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }
    }

    #[Route('/all', methods: ['GET'])]
    public function getAllPublicTrips(TripRepository $tripRepository): Response
    {
        return $this->json($tripRepository->findByStatus(true), Response::HTTP_OK, [], ['groups' => 'trip:read']);
    }

    /**
     * Get one trip.
     * @param Trip|null $trip
     * @param TripRepository $tripRepository
     * @return Response
     */
    #[Route('/{id}', methods: ['GET'])]
    public function getOneTrip(?Trip $trip, TripRepository $tripRepository): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        // restrictions?

        return $this->json($trip, Response::HTTP_OK, [], ['groups' => 'trip:read']);
    }

    /**
     * Create a new trip.
     * @param TripService $tripService
     * @param Request $request
     * @return Response
     */
    #[Route('/new', methods: ['POST'])]
    public function createTrip(
        TripService $tripService,
        Request     $request,
    ): Response
    {
        $trip = $tripService->createTrip($this->getUser(), $request);
        return $this->json($trip, Response::HTTP_CREATED, [], ['groups' => 'trip:read']);
    }

    #[Route('/{id}/image', methods: ['POST'])]
    public function addImageToTrip(
        ?Trip $trip,
        TripService $tripService,
        Request     $request,
    ):Response
    {
        $trip = $tripService->addImageToTrip($trip, $request);
        return $this->json($trip, Response::HTTP_CREATED, [], ['groups' => 'trip:read']);
    }

    /**
     * Edit a trip (textual information or dates).
     * @param TripService $tripService
     * @param Request $request
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/{id}/edit', methods: ['PUT'])]
    #[Route('/{id}/edit-dates', name: "app_trip_edit-dates", methods: ['PUT'])]
    public function editTrip(TripService $tripService, Request $request, ?Trip $trip, TripParticipantRepository $tripParticipantRepository): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }

        if ($request->get('_route') == "app_trip_edit-dates") {
            $calledFunction = $tripService->editTripDates($trip, $request);
        } else {
            $calledFunction = $tripService->editTripNameAndDescription($trip, $request);
        }

        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        $editedTrip = $calledFunction;
        return $this->json($editedTrip, Response::HTTP_OK, [], ['groups' => 'trip:read']);
    }

    /**
     * Delete multiple trips.
     * @param TripService $tripService
     * @param Request $request
     * @param TripRepository $tripRepository
     * @return Response
     */
    #[Route('/delete', methods: ['DELETE'])]
    public function deleteTrips(TripService $tripService, Request $request, TripRepository $tripRepository): Response
    {
        $data = $request->toArray();
        foreach ($data["tripIds"] as $tripId) {
            $trip = $tripRepository->findOneBy(["id" => $tripId]);
            if (!$trip) {
                return $this->json("trip with id " . $tripId . " not found", Response::HTTP_NOT_FOUND);
            }
            if ($trip->getOwner() !== $this->getUser()) {
                return $this->json("access denied to trip with id " . $tripId, Response::HTTP_FORBIDDEN);
            }
            $tripService->deleteTrip($trip);
        }
        return $this->json("trips successfully deleted", Response::HTTP_OK);
    }

    /**
     * Delete a trip.
     * @param Trip|null $trip
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function deleteTrip(?Trip $trip, TripService $tripService): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if ($trip->getOwner() !== $this->getUser()) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }
        $tripService->deleteTrip($trip);
        return $this->json("trip successfully deleted", Response::HTTP_OK);
    }

    /**
     * Add participants to your trip.
     * @param Trip|null $trip
     * @param Request $request
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/{id}/add-people', methods: ['POST'])]
    public function addPeopleToTrip(?Trip $trip, Request $request, TripService $tripService): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if ($this->getUser() !== $trip->getOwner()) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $response = $tripService->addPeopleToTrip($trip, $request, $this->getUser());
        return $this->json($response); // MODIF RETURN DANS SERVICE
    }

    /**
     * Remove people from your trip.
     * @param Trip|null $trip
     * @param Request $request
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/{id}/remove-people', methods: ['POST'])]
    public function removePeopleFromTrip(?Trip $trip, Request $request, TripService $tripService): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if ($trip->getOwner() !== $this->getUser()) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }
        $response = $tripService->removePeopleFromTrip($trip, $request);
        return $this->json($response);
    }

    /**
     * Change participant role.
     * @param Trip|null $trip
     * @param Request $request
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/{id}/change-permissions', methods: ['POST'])]
    public function changeParticipantPermissions(?Trip $trip, Request $request, TripService $tripService): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        if ($trip->getOwner() !== $this->getUser()) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $response = $tripService->changeStatusOfParticipants($trip, $request);
        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * Get public users which haven't been invited yet.
     * @param Trip|null $trip
     * @param TripInviteService $tripInviteService
     * @return Response
     */
    #[Route('/{id}/invitable-people', methods: ['GET'])]
    public function getInvitablePeople(
        ?Trip $trip,
        TripInviteService $tripInviteService,

    ): Response
    {
        $people = $tripInviteService->getInvitablePeople($trip, $this->getUser());
        return $this->json($people, Response::HTTP_OK, [], ['groups' => 'users:index']);
    }

}
