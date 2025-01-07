<?php

namespace App\Controller;

use App\Entity\TripInvite;
use App\Entity\User;
use App\Service\TripInviteService;
use App\Service\TripService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/invite')]
class TripInviteController extends AbstractController
{
    /**
     * Get all invites of a user.
     * @param User|null $user
     * @return Response
     */
    #[Route('/all/user/{id}', methods: ['GET'])]
    public function indexAllInvitesOfUser(?User $user): Response
    {
        if (!$user) {
            return $this->json("User not found", Response::HTTP_NOT_FOUND);
        }
        if($user !== $this->getUser()) {
            return $this->json("Access denied", Response::HTTP_FORBIDDEN);
        }

        $invites = $user->getReceivedTripInvites();
        return $this->json($invites, Response::HTTP_OK, [], ['groups' => 'invites:read']);
    }

    /**
     * Accept a trip invite.
     * @param TripInviteService $tripInviteService
     * @param TripInvite|null $invite
     * @return Response
     */
    #[Route('/{id}/accept', methods: ['GET'])]
    public function acceptTripInvite(TripInviteService $tripInviteService, ?TripInvite $invite): Response
    {
        if (!$invite) {
            return $this->json("Invite not found", Response::HTTP_NOT_FOUND);
        }
        if($invite->getRecipient() !== $this->getUser()) {
            return $this->json("Access denied", Response::HTTP_FORBIDDEN);
        }
        $response = $tripInviteService->acceptInvite($invite);
        return $this->json($response, Response::HTTP_OK);
    }


    /**
     * Decline trip invite.
     * @param TripInviteService $tripInviteService
     * @param TripInvite|null $invite
     * @return Response
     */
    #[Route('/{id}/decline', methods: ['GET'])]
    public function declineTripInvite(TripInviteService $tripInviteService, ?TripInvite $invite): Response
    {
        if (!$invite) {
            return $this->json("Invite not found", Response::HTTP_NOT_FOUND);
        }
        if($invite->getRecipient() !== $this->getUser()) {
            return $this->json("Access denied", Response::HTTP_FORBIDDEN);
        }
        $response = $tripInviteService->declineInvite($invite);
        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * Retract a trip invite.
     * @param TripInviteService $tripInviteService
     * @param TripInvite|null $invite
     * @return Response
     */
    #[Route('/{id}/retract', methods: ['GET'])]
    public function retractTripInvite(TripInviteService $tripInviteService, ?TripInvite $invite): Response
    {
        if (!$invite) {
            return $this->json("Invite not found", Response::HTTP_NOT_FOUND);
        }
        if($invite->getSender() !== $this->getUser()) {
            return $this->json("Access denied", Response::HTTP_FORBIDDEN);
        }
        $res = $tripInviteService->retractInvite($invite);
        return $this->json($res, Response::HTTP_OK);
    }
}
