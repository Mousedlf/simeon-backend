<?php

namespace App\Controller;

use App\Entity\DayOfTrip;
use App\Entity\Trip;
use App\Service\ItineraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/trip')]
class DayOfTripController extends AbstractController
{
    #[Route('/{id}/day/{dayId}', methods: ['GET'])]
    public function getItineraryOfOneDay(
        #[MapEntity(id: 'id')] ?Trip $trip,
        #[MapEntity(id: 'dayId')] ?DayOfTrip $dayOfTrip,
        ItineraryService $itineraryService,
        EntityManagerInterface $manager
    ): Response
    {
        if(!$trip || !$dayOfTrip) {
            return $this->json('trip or day not found', Response::HTTP_NOT_FOUND);
        }
        //$reservations
        $activities = $dayOfTrip->getActivities();

        return $this->json($activities, Response::HTTP_OK, [],['groups' => ['day:read']]);

    }
}
