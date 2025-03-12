<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\TripParticipant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class MessageService
{

    function __construct(
        public SerializerInterface       $serializer,
        public EntityManagerInterface    $manager,
    )
    {
    }

    /**
     * Add a message.
     * @param TripParticipant $participant
     * @param Request $request
     * @param Conversation $conversation
     * @return Message
     */
    function newMessage(TripParticipant $participant, Request $request, Conversation $conversation): Message // préciser type
    {
        $message = $this->serializer->deserialize($request->getContent(), Message::class, 'json');
        $message->setAuthor($participant);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setConversation($conversation);
        $message->setPinned(false);

        $this->manager->persist($message);
        $this->manager->flush();

        return $message;

    }

    /**
     * Edit a message.
     * @param Message $message
     * @param Request $request
     * @return Message
     */
    function editMessage(Message $message, Request $request): Message
    {
        $editedMessage = $this->serializer->deserialize($request->getContent(), Message::class, 'json');

        $message->setUpdatedAt(new \DateTimeImmutable());
        $message->setContent($editedMessage->getContent());
        $this->manager->persist($message);
        $this->manager->flush();

        return $message;
    }

    /**
     * Delete a message.
     * @param Message $message
     * @return string
     */
    public function deleteMessage(Message $message): string
    {
        $this->manager->remove($message);
        $this->manager->flush();
        return "message successfully deleted";
    }

    /**
     * Pin a message.
     * @param Message $message
     * @return Message
     */
    function pinMessage(Message $message): Message
    {
        $message->setPinned(true);
        $this->manager->persist($message);
        $this->manager->flush();

        return $message;
    }

}