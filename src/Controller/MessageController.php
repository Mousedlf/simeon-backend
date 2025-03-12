<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Service\MessageService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversation')]
class MessageController extends AbstractController
{
    /**
     * Add a new message.
     * @param Conversation|null $conversation
     * @param Request $request
     * @param MessageService $messageService
     * @return Response
     */
    #[Route('/{id}/message/new', methods: ['POST'])]
    public function newMessage(
        ?Conversation $conversation,
        Request $request,
        MessageService $messageService,
    ): Response
    {
        if(!$conversation){
            return $this->json(["conversation not found"], Response::HTTP_NOT_FOUND);
        }

        $members = $conversation->getMembers();
        foreach ($members as $member) {
            if($member->getParticipant() === $this->getUser()) {
                $participant = $member;
            }
        }

        if(!$participant){
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $message = $messageService->newMessage($participant, $request, $conversation);

        return $this->json($message, Response::HTTP_CREATED, [], ['groups' => ['message:read']]);
    }

    /**
     * Edit a message.
     * @param Conversation|null $conversation
     * @param Message|null $message
     * @param Request $request
     * @param MessageService $messageService
     * @return Response
     */
    #[Route('/{convId}/message/{messageId}/edit', methods: ['PUT'])]
    public function editMessage(
        #[MapEntity(id: 'convId')] ?Conversation $conversation,
        #[MapEntity(id: 'messageId')] ?Message $message,
        Request $request,
        MessageService $messageService,
    ): Response
    {
        if(!$conversation || !$message){
            return $this->json(["conversation or message not found"], Response::HTTP_NOT_FOUND);
        }

        $members = $conversation->getMembers();
        foreach ($members as $member) {
            if($member->getParticipant() === $this->getUser()) {
                $participant = $member;
            }
        }

        if(!$participant){
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        if($message->getAuthor() !== $participant) {
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        $editedMessage = $messageService->editMessage($message, $request);

        return $this->json($editedMessage, Response::HTTP_OK, [], ['groups' => ['message:read']]);

    }

    #[Route('/{convId}/message/{messageId}/pin', methods: ['GET'])]
    public function pinMessage(
        #[MapEntity(id: 'convId')] ?Conversation $conversation,
        #[MapEntity(id: 'messageId')] ?Message $message,
        Request $request,
        MessageService $messageService,
    ): Response
    {
        if(!$conversation || !$message){
            return $this->json(["conversation or message not found"], Response::HTTP_NOT_FOUND);
        }

        $members = $conversation->getMembers();
        foreach ($members as $member) {
            if($member->getParticipant() === $this->getUser()) {
                $participant = $member;
            }
        }

        if(!$participant){
            return $this->json("access denied", Response::HTTP_FORBIDDEN);
        }

        return $this->json("message pinned", Response::HTTP_OK, [], ['groups' => ['message:read']]);
    }
}
