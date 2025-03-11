<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Service\TripService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversation')]
class ConversationController extends AbstractController
{
    /**
     * Get all conversations of a user.
     * @param TripService $tripService
     * @return Response
     */
    #[Route('/all', methods: ['GET'])]
    public function getAllConversationsOfUser(TripService $tripService): Response
    {
        $currentUser = $this->getUser();
        $conversations = [];

        $trips = $tripService->getTripsOfUser($currentUser);
        foreach ($trips as $trip) {
            $conversation = $trip->getConversation();

            $members = $conversation->getMembers();

            foreach ($members as $member) {
                if($member->getParticipant() === $currentUser) {
                    $conversations[] = $trip->getConversation();
                }
            }
        }
        return $this->json($conversations, Response::HTTP_OK, [], ['groups' => 'conversation:read']);
    }

    /**
     * Show one conversation with its messages.
     * @param Conversation|null $conversation
     * @return Response
     */
    #[Route('/{id}', methods: ['GET'])]
    public function getOneConversation(?Conversation $conversation): Response
    {
        if (!$conversation) {
            return $this->json("conversation not found", Response::HTTP_NOT_FOUND);
        }

        // restriction si membre alors ok

        return $this->json($conversation, Response::HTTP_OK, [], ['groups' => 'conversation:read']);
    }

}
