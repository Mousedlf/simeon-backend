<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Trip;
use App\Enum\ParticipantStatus;
use App\Repository\TripParticipantRepository;
use App\Service\DocumentService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/document')]
class DocumentController extends AbstractController
{

    /**
     * Get one document.
     * @param Document|null $document
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @return Response
     */
    #[Route('/{id}/trip/{tripId}', methods: ['GET'])]
    public function getDocument(
        #[MapEntity(id: 'id')] ?Document $document,
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        TripParticipantRepository $tripParticipantRepository,
    ): Response
    {
        if (!$trip) {
            return $this->json("trip not found", Response::HTTP_NOT_FOUND);
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        if (!$participant) {
            return $this->json("not part of this trip", Response::HTTP_FORBIDDEN);
        }

        return $this->json($document, Response::HTTP_OK, [], ['groups' => 'document:read']);
    }


    /**
     * Add a document to a trip
     * @param Document|null $document
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @param Request $request
     * @param DocumentService $documentService
     * @return Response
     */
    #[Route('/new/trip/{tripId}', methods: ['POST'])]
    #[Route('/{id}/trip/{tripId}/edit', name: 'edit_trip_document' ,methods: ['POST'])]
    public function addOrEditTripDocument(
        #[MapEntity(id: 'id')] ?Document $document,
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        TripParticipantRepository $tripParticipantRepository,
        Request $request,
        DocumentService $documentService
    ): Response
    {
        if($request->get('_route') == "edit_trip_document"){
            if(!$document){return $this->json("document not found", Response::HTTP_NOT_FOUND);}
        }
        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);

        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        if($participant->getRole() == ParticipantStatus::VIEWER){
            return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        }

        $jsonData = $request->request->get('data');
        $file = $request->files->get('file');

        if($request->get('_route') == "edit_trip_document"){
            $calledFunction = $documentService->editDocumentOfATrip($document, $participant, $file, $jsonData);
        } else {
            $calledFunction = $documentService->addDocumentToTrip($trip, $participant, $file, $jsonData);
        }

        $document = $calledFunction;

        return $this->json($document, Response::HTTP_OK, [], ['groups' => ['document:read']]);
    }

    /**
     * Add a document to a user.
     * @param Request $request
     * @param DocumentService $documentService
     * @return Response
     */
    #[Route('/new/user', methods: ['POST'])]
    public function addDocumentsToCurrentUser(
        Request $request,
        DocumentService $documentService
    ): Response
    {
        $currentUser = $this->getUser();
        $jsonData = $request->request->get('data');
        $file = $request->files->get('file');

        $document = $documentService->addDocumentToUser($currentUser, $file, $jsonData);
        return $this->json($document, Response::HTTP_OK, [], ['groups' => ['document:read']]);
    }

    /**
     * Delete a document of a trip.
     * @param Document|null $document
     * @param Trip|null $trip
     * @param TripParticipantRepository $tripParticipantRepository
     * @param DocumentService $documentService
     * @return Response
     */
    #[Route('/{id}/trip/{tripId}/delete', methods: ['DELETE'])]
    public function deleteDocumentOfTrip(
        #[MapEntity(id: 'id')] ?Document $document,
        #[MapEntity(id: 'tripId')] ?Trip $trip,
        TripParticipantRepository $tripParticipantRepository,
        DocumentService $documentService
    ): Response
    {
        if(!$document){return $this->json("document not found", Response::HTTP_NOT_FOUND);}

        $participant = $tripParticipantRepository->findOneParticipant($this->getUser(), $trip);
        switch ($participant):
            case null:
                return $this->json("access denied", Response::HTTP_FORBIDDEN);
            case $participant->getRole() == ParticipantStatus::VIEWER:
                return $this->json("permissions not granted", Response::HTTP_FORBIDDEN);
        endswitch;

        $documentService->deleteDocument($document);
        return $this->json("document successfully deleted", Response::HTTP_OK);
    }

    /**
     * Delete a document of a trip.
     * @param Document|null $document
     * @param DocumentService $documentService
     * @return Response
     */
    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function deleteDocumentOfUser(
        #[MapEntity(id: 'id')] ?Document $document,
        DocumentService $documentService
    ): Response
    {
        if(!$document){return $this->json("document not found", Response::HTTP_NOT_FOUND);}

       if($document->getOfUser() !== $this->getUser()){
           return $this->json("permission not granted", Response::HTTP_FORBIDDEN);
       }

        $documentService->deleteDocument($document);
        return $this->json("document successfully deleted", Response::HTTP_OK);

    }



}
