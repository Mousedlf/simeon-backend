<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense:new', 'expense:index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['expense:new', 'expense:index'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['expense:new', 'expense:read', 'expense:index'])]
    private ?float $sum = null;

    #[ORM\Column]
    #[Groups(['expense:new'])]
    private ?bool $divide = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: true, onDelete:"CASCADE")]
    #[Groups(['expense:new', 'expense:index'])]
    private ?DayOfTrip $dayOfTrip = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    #[Groups(['expense:new'])]
    private ?Trip $trip = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['expense:new','expense:index'])]
    private ?string $paymentMethod = null;

    /**
     * @var Collection<int, TripParticipant>
     */
    #[ORM\ManyToMany(targetEntity: TripParticipant::class, inversedBy: 'personal', cascade: ['persist'])]
    #[Groups(['expense:new','expense:index'])]
    private Collection $divideBetween;

    #[ORM\ManyToOne(inversedBy: 'paidExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['expense:new','expense:index'])]
    private ?TripParticipant $paidBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['expense:new', 'expense:index'])]
    private ?bool $personal = null;

    public function __construct()
    {
        $this->divideBetween = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): static
    {
        $this->sum = $sum;

        return $this;
    }

    public function isDivide(): ?bool
    {
        return $this->divide;
    }

    public function setDivide(bool $divide): static
    {
        $this->divide = $divide;

        return $this;
    }

    public function getDayOfTrip(): ?DayOfTrip
    {
        return $this->dayOfTrip;
    }

    public function setDayOfTrip(?DayOfTrip $dayOfTrip): static
    {
        $this->dayOfTrip = $dayOfTrip;

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

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return Collection<int, TripParticipant>
     */
    public function getDivideBetween(): Collection
    {
        return $this->divideBetween;
    }

    public function addDivideBetween(TripParticipant $divideBetween): static
    {
        if (!$this->divideBetween->contains($divideBetween)) {
            $this->divideBetween->add($divideBetween);
        }

        return $this;
    }

    public function removeDivideBetween(TripParticipant $divideBetween): static
    {
        $this->divideBetween->removeElement($divideBetween);

        return $this;
    }

    public function getPaidBy(): ?TripParticipant
    {
        return $this->paidBy;
    }

    public function setPaidBy(?TripParticipant $paidBy): static
    {
        $this->paidBy = $paidBy;

        return $this;
    }

    public function isPersonal(): ?bool
    {
        return $this->personal;
    }

    public function setPersonal(?bool $personal): static
    {
        $this->personal = $personal;

        return $this;
    }
}
