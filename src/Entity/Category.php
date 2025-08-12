<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[ORM\DiscriminatorMap(["expense_category" => ExpenseCategory::class, "itinerary_category" => ActivityCategory::class])]
abstract class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense:new','expense:index', 'day:read', 'day:index', 'activity:read', 'expenseCategory:index', 'activityCategory:index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['expense:new','expense:index', 'day:read', 'day:index', 'activity:read', 'expenseCategory:index', 'activityCategory:index'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'custom_categories')]
    #[Groups(['expense:new','expense:index', 'day:read', 'day:index', 'activity:read'])]
    private ?User $ofUser = null;

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

    public function getOfUser(): ?User
    {
        return $this->ofUser;
    }

    public function setOfUser(?User $ofUser): static
    {
        $this->ofUser = $ofUser;

        return $this;
    }
}
