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

    function newMessage(TripParticipant $participant, Request $request, Conversation $conversation) // préciser type
    {
        $message = $this->serializer->deserialize($request->getContent(), Message::class, 'json');
        $message->setAuthor($participant);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setConversation($conversation);

        $this->manager->persist($message);
        $this->manager->flush();

        return $message;

    }

    function editMessage(Message $message, Request $request)
    {
        $editedMessage = $this->serializer->deserialize($request->getContent(), Message::class, 'json');
        $editedMessage->setUpdatedAt(new \DateTimeImmutable());
        $this->manager->persist($editedMessage);
        $this->manager->flush();

        return $editedMessage;
    }

}