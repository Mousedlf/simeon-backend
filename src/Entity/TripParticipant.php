<?php

namespace App\Entity;

use App\Enum\ParticipantStatus;
use App\Repository\TripParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripParticipantRepository::class)]
class TripParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read', 'expense:new', 'conversation:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    #[Groups(['trip:read', 'expense:new', 'expense:index', 'conversation:read'])]
    private ?User $participant = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['trip:read', 'conversation:read'])]
    private ?ParticipantStatus $role = null;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\ManyToMany(targetEntity: Expense::class, mappedBy: 'divideBetween')]
    private Collection $sharedExpenses;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'paidBy')]
    private Collection $paidExpenses;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'members')]
    private Collection $conversations;

    public function __construct()
    {
        $this->sharedExpenses = new ArrayCollection();
        $this->paidExpenses = new ArrayCollection();
        $this->conversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;

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

    /**
     * @return Collection<int, Expense>
     */
    public function getPersonal(): Collection
    {
        return $this->sharedExpenses;
    }

    public function addSharedExpenses(Expense $sharedExpenses): static
    {
        if (!$this->sharedExpenses->contains($sharedExpenses)) {
            $this->sharedExpenses->add($sharedExpenses);
            $sharedExpenses->addDivideBetween($this);
        }

        return $this;
    }

    public function removeSharedExpense(Expense $sharedExpenses): static
    {
        if ($this->sharedExpenses->removeElement($sharedExpenses)) {
            $sharedExpenses->removeDivideBetween($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getPaidExpenses(): Collection
    {
        return $this->paidExpenses;
    }

    public function addPaidExpense(Expense $paidExpense): static
    {
        if (!$this->paidExpenses->contains($paidExpense)) {
            $this->paidExpenses->add($paidExpense);
            $paidExpense->setPaidBy($this);
        }

        return $this;
    }

    public function removePaidExpense(Expense $paidExpense): static
    {
        if ($this->paidExpenses->removeElement($paidExpense)) {
            // set the owning side to null (unless already changed)
            if ($paidExpense->getPaidBy() === $this) {
                $paidExpense->setPaidBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->addMember($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeMember($this);
        }

        return $this;
    }
}
