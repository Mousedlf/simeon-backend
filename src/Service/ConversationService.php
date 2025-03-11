<?php

namespace App\Service;

use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;

class ConversationService
{

    public function __construct(
        public EntityManagerInterface    $manager,
    )
    {
    }

    /**
     * Delete a conversation if less than 2 people are left.
     * @param Conversation $conversation
     * @return string
     */
    public function deleteConversation(Conversation $conversation): string
    {
        if(count($conversation->getMembers()) > 1){
            return "conversation can only be deleted as the last person leaves";
        }

        $this->manager->remove($conversation);
        $this->manager->flush();
        return "conversation deleted successfully";

    }

}