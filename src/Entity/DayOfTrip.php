<?php

namespace App\Entity;

use App\Repository\DayOfTripRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DayOfTripRepository::class)]
class DayOfTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read', 'expense:new', 'expense:index'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'daysOfTrip')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Trip $trip = null;

    #[ORM\Column]
    #[Groups(['trip:read', 'expense:new', 'expense:index'])]
    private ?DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'dayOfTrip', orphanRemoval: true)]
    private Collection $expenses;

    /**
     * @var Collection<int, TripActivity>
     */
    #[ORM\ManyToMany(targetEntity: TripActivity::class, mappedBy: 'day')]
    private Collection $activities;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->activities = new ArrayCollection();
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

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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
            $expense->setDayOfTrip($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getDayOfTrip() === $this) {
                $expense->setDayOfTrip(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TripActivity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(TripActivity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->addDay($this);
        }

        return $this;
    }

    public function removeActivity(TripActivity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removeDay($this);
        }

        return $this;
    }
}
