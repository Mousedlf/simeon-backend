<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?\DateTimeImmutable $added_at = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?TripActivity $tripActivity = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['participant:read','trip:read', 'document:read'])]
    private ?User $ofUser = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['document:read'])]
    private ?TripParticipant $addedBy = null;

    #[ORM\OneToOne(inversedBy: 'document', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trip:read', 'document:read'])]
    private ?DocumentFile $file = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['document:read'])]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[Groups(['trip:read', 'document:read'])]
    private ?ActivityCategory $category = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->added_at;
    }

    public function setAddedAt(\DateTimeImmutable $added_at): static
    {
        $this->added_at = $added_at;

        return $this;
    }

    public function getTripActivity(): ?TripActivity
    {
        return $this->tripActivity;
    }

    public function setTripActivity(?TripActivity $tripActivity): static
    {
        $this->tripActivity = $tripActivity;

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

    public function getAddedBy(): ?TripParticipant
    {
        return $this->addedBy;
    }

    public function setAddedBy(?TripParticipant $addedBy): static
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getFile(): ?DocumentFile
    {
        return $this->file;
    }

    public function setFile(DocumentFile $file): static
    {
        $this->file = $file;

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

    public function getCategory(): ?ActivityCategory
    {
        return $this->category;
    }

    public function setCategory(?ActivityCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
