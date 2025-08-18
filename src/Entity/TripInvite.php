<?php

namespace App\Entity;

use App\Enum\InviteStatus;
use App\Enum\ParticipantStatus;
use App\Repository\TripInviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripInviteRepository::class)]
class TripInvite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invites:read', 'trip:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['invites:read'])]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'sentTripInvites')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invites:read'])]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedTripInvites')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trip:read'])]
    private ?User $recipient = null;

    #[ORM\Column]
    #[Groups(['invites:read', 'trip:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'sentInvites')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    #[Groups(['invites:read'])]
    private ?Trip $trip = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invites:read', 'trip:read'])]
    private ?ParticipantStatus $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invites:read', 'trip:read'])]
    private ?InviteStatus $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getRole(): ?ParticipantStatus
    {
        return $this->role;
    }

    public function setRole(?ParticipantStatus $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): ?InviteStatus
    {
        return $this->status;
    }

    public function setStatus(?InviteStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
