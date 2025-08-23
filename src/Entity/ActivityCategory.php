<?php

namespace App\Entity;

use App\Repository\ActivityCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityCategoryRepository::class)]
class ActivityCategory extends Category
{
    /**
     * @var Collection<int, TripActivity>
     */
    #[ORM\OneToMany(targetEntity: TripActivity::class, mappedBy: 'category')]
    private Collection $tripActivities;

    /**
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'category')]
    private Collection $documents;

    public function __construct()
    {
        $this->tripActivities = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * @return Collection<int, TripActivity>
     */
    public function getTripActivities(): Collection
    {
        return $this->tripActivities;
    }

    public function addTripActivity(TripActivity $tripActivity): static
    {
        if (!$this->tripActivities->contains($tripActivity)) {
            $this->tripActivities->add($tripActivity);
            $tripActivity->setCategory($this);
        }

        return $this;
    }

    public function removeTripActivity(TripActivity $tripActivity): static
    {
        if ($this->tripActivities->removeElement($tripActivity)) {
            // set the owning side to null (unless already changed)
            if ($tripActivity->getCategory() === $this) {
                $tripActivity->setCategory(null);
            }
        }

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
            $document->setCategory($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getCategory() === $this) {
                $document->setCategory(null);
            }
        }

        return $this;
    }
}

