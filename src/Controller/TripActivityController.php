<?php

namespace App\Controller;

use App\Entity\DayOfTrip;
use App\Entity\Trip;
use App\Entity\TripActivity;
use App\Enum\ParticipantStatus;
use App\Repository\TripParticipantRepository;
use App\Service\ActivityService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/activity')]
class TripActivityController extends AbstractController
{
    #[Route('/new/trip/{tripId}', name: "app_trip_new-activity", methods: ['POST'])]
    #[Route('/edit/{id}/trip/{tripId}/day/{dayId}', name: "app_trip_edit-activity", methods: ['PUT'])]
    public function addTripActivity(
        #[MapEntity(id: 'id')] ?TripActivity $tripActivity,
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        #[MapEntity(id: 'dayId')] ?DayOfTrip $dayOfTrip,
        ActivityService $activityService,
        Request $request,
        TripParticipantRepository $tripParticipantRepository
    ) : Response
    {
        if(!$trip) {
            return $this->json('trip not found', Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        if ($request->get('_route') == "app_trip_edit-activity") {
            if(!$tripActivity || !$dayOfTrip) {
                return $this->json("trip activity or day not found", Response::HTTP_NOT_FOUND);
            }
            $calledFunction = $activityService->editActivityOfTrip($dayOfTrip, $request, $tripActivity);
        } else {

            $calledFunction = $activityService->addActivityToTrip($request);
        }

        $res = $calledFunction;

        return $this->json($res, Response::HTTP_OK, [], ['groups' => ['day:read']]);
    }

    #[Route('/{id}/trip/{tripId}/delete', name: "app_trip_delete-activity", methods: ['DELETE'])]
    public function deleteActivity(
        #[MapEntity(id: 'id')] ?TripActivity $tripActivity,
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        TripParticipantRepository $tripParticipantRepository,
        ActivityService $activityService,
    ) : Response
    {
        if(!$tripActivity || !$trip) {
            return $this->json('trip or activity not found', Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        $activityService->deleteActivity($tripActivity);
        return $this->json("activity successfully deleted", Response::HTTP_OK);
    }

    #[Route('/trip/{tripId}/day/{dayId}/reorder', methods: ['POST'])]
    public function reorderTripActivitiesOfDay(
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        #[MapEntity(id: 'dayId')] ?DayOfTrip $dayOfTrip,
        Request $request,
        TripParticipantRepository $tripParticipantRepository,
        ActivityService $activityService,
    ):Response
    {
        if(!$trip || !$dayOfTrip) {
            return $this->json('trip or day  not found', Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        $res = $activityService->reorderActivities($dayOfTrip, $request);
        return $this->json($res, Response::HTTP_OK, [], ['groups' => ['day:read']]);

    }
}