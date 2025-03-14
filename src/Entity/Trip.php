<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read','expense:new', 'expense:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trip:read','invites:read','expense:new', 'expense:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'createdTrips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trip:read','invites:read'])]
    private ?User $owner = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['trip:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['trip:read','invites:read'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
    #[Groups(['trip:read','invites:read'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbOfDays = null;


    /**
     * @var Collection<int, TripInvite>
     */
    #[ORM\OneToMany(targetEntity: TripInvite::class, mappedBy: 'trip', cascade: ["remove"], orphanRemoval: true)]
    #[Groups(['trip:read'])]
    private Collection $sentInvites;

    /**
     * @var Collection<int, TripParticipant>
     */
    #[ORM\OneToMany(targetEntity: TripParticipant::class, mappedBy: 'trip', cascade: ["remove"], orphanRemoval: true)]
    #[Groups(['trip:read'])]
    private Collection $participants;

    #[ORM\Column(nullable: true)]
    private ?bool $public = null;

    /**
     * @var Collection<int, DayOfTrip>
     */
    #[ORM\OneToMany(targetEntity: DayOfTrip::class, mappedBy: 'trip', cascade: ["remove"], orphanRemoval: true)]
    #[Groups(['trip:read'])]
    private Collection $daysOfTrip;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'trip', cascade: ["remove"], orphanRemoval: true)]
    #[Groups(['expense:read'])]
    private Collection $expenses;

    #[ORM\OneToOne(inversedBy: 'trip', cascade: ['persist', 'remove'])]
    private ?Conversation $conversation = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['trip:read'])]
    private ?float $budget = null;

    public function __construct()
    {
        $this->sentInvites = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->daysOfTrip = new ArrayCollection();
        $this->expenses = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNbOfDays(): ?int
    {
        return $this->nbOfDays;
    }

    public function setNbOfDays(?int $nbOfDays): static
    {
        $this->$nbOfDays = $nbOfDays;

        return $this;
    }


    /**
     * @return Collection<int, TripInvite>
     */
    public function getSentInvites(): Collection
    {
        return $this->sentInvites;
    }

    public function addSentInvite(TripInvite $sentInvite): static
    {
        if (!$this->sentInvites->contains($sentInvite)) {
            $this->sentInvites->add($sentInvite);
            $sentInvite->setTrip($this);
        }

        return $this;
    }

    public function removeSentInvite(TripInvite $sentInvite): static
    {
        if ($this->sentInvites->removeElement($sentInvite)) {
            // set the owning side to null (unless already changed)
            if ($sentInvite->getTrip() === $this) {
                $sentInvite->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TripParticipant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(TripParticipant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setTrip($this);
        }

        return $this;
    }

    public function removeParticipant(TripParticipant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getTrip() === $this) {
                $participant->setTrip(null);
            }
        }

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): static
    {
        $this->public = $public;

        return $this;
    }


    public function getDaysOfTrip(): Collection
    {
        return $this->daysOfTrip;
    }

    public function addDaysOfTrip(DayOfTrip $daysOfTrip): static
    {
        if (!$this->daysOfTrip->contains($daysOfTrip)) {
            $this->daysOfTrip->add($daysOfTrip);
            $daysOfTrip->setTrip($this);
        }

        return $this;
    }

    public function removeDaysOfTrip(DayOfTrip $daysOfTrip): static
    {
        if ($this->daysOfTrip->removeElement($daysOfTrip)) {
            // set the owning side to null (unless already changed)
            if ($daysOfTrip->getTrip() === $this) {
                $daysOfTrip->setTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setTrip($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getTrip() === $this) {
                $expense->setTrip(null);
            }
        }

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }
}
