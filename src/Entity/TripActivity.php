<?php

namespace App\Entity;

use App\Repository\TripActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripActivityRepository::class)]
class TripActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var Collection<int, DayOfTrip>
     */
    #[ORM\ManyToMany(targetEntity: DayOfTrip::class, inversedBy: 'activities')]
    private Collection $day;

    #[ORM\ManyToOne(inversedBy: 'tripActivities')]
    private ?ActivityCategory $category = null;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'tripActivity')]
    private Collection $documents;

    public function __construct()
    {
        $this->day = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, DayOfTrip>
     */
    public function getDay(): Collection
    {
        return $this->day;
    }

    public function addDay(DayOfTrip $day): static
    {
        if (!$this->day->contains($day)) {
            $this->day->add($day);
        }

        return $this;
    }

    public function removeDay(DayOfTrip $day): static
    {
        $this->day->removeElement($day);

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

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setTripActivity($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getTripActivity() === $this) {
                $document->setTripActivity(null);
            }
        }

        return $this;
    }


}
