<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Repository\TripRepository;
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
     * @param User $user
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/all/{id}', methods:['GET'])]
    public function getAllTripsOfUser(User $user, TripService $tripService): Response
    {
        if($user->isPublic() OR $user === $this->getUser()){
            $trips = $tripService->getTripsOfUser($user);
            return $this->json($trips, 403, [], ['groups' => 'trip:read']);
        } else {
            return $this->json("access denied", 403);
        }
    }

    /**
     * Create a new trip.
     * @param TripService $tripService
     * @param Request $request
     * @return Response
     */
    #[Route('/new', methods:['POST'])]
    public function createTrip(
        TripService $tripService,
        Request $request,
    ): Response
    {
       $trip = $tripService->createTrip($this->getUser(), $request);
       return $this->json($trip, Response::HTTP_CREATED, [], ['groups' => 'trip:read']);

    }

    /**
     * Edit a trip.
     * @param TripService $tripService
     * @param Request $request
     * @param Trip $trip
     * @return Response
     */
    #[Route('/{id}/edit', methods:['PUT'])]
    public function editTrip(TripService $tripService, Request $request, Trip $trip): Response
    {
        if($this->getUser() !== $trip->getOwner()){
            return $this->json("access denied", 403);
        }
        $editedTrip = $tripService->editTripNameAndDescription($trip, $request);
        return $this->json($editedTrip, Response::HTTP_OK, [], ['groups' => 'trip:read']);
    }

    /**
     * Delete a trip.
     * @param Trip $trip
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/{id}/delete', methods:['DELETE'])]
    public function deleteTrip(?Trip $trip, TripService $tripService):Response
    {
        if($trip->getOwner() !== $this->getUser()){
            return $this->json("access denied", 403);
        }
        $tripService->deleteTrip($trip);
        return $this->json("trip sucessfully deleted", Response::HTTP_OK);
    }

    /**
     * Delete multiple trips.
     * @param TripService $tripService
     * @param Request $request
     * @return Response
     */
    #[Route('/delete', methods:['DELETE'])]
    public function deleteTrips(TripService $tripService, Request $request, TripRepository $tripRepository):Response
    {
        $data = $request->toArray();
        foreach ($data["tripIds"] as $tripId) {
            $trip = $tripRepository->findOneBy(["id" => $tripId]);
            if(!$trip){
                return $this->json("trip with id " .$tripId ." not found", Response::HTTP_NOT_FOUND);
            }
            if($trip->getOwner() !== $this->getUser()){
                return $this->json("access denied to trip with id ".$tripId, 403);
            }
            $tripService->deleteTrip($trip);
        }
        return $this->json("trips sucessfully deleted", Response::HTTP_OK);
    }

}
