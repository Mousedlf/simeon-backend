<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['conversation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['conversation:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, TripParticipant>
     */
    #[ORM\ManyToMany(targetEntity: TripParticipant::class, inversedBy: 'conversations')]
    #[Groups(['conversation:read'])]
    private Collection $members;

    #[ORM\OneToOne(mappedBy: 'conversation', cascade: ['persist', 'remove'])]
    private ?Trip $trip = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation', orphanRemoval: true)]
    #[Groups(['conversation:read'])]
    private Collection $messages;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, TripParticipant>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(TripParticipant $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(TripParticipant $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        // unset the owning side of the relation if necessary
        if ($trip === null && $this->trip !== null) {
            $this->trip->setConversation(null);
        }

        // set the owning side of the relation if necessary
        if ($trip !== null && $trip->getConversation() !== $this) {
            $trip->setConversation($this);
        }

        $this->trip = $trip;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}
