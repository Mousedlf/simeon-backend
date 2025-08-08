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

    public function __construct()
    {
        $this->tripActivities = new ArrayCollection();
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
}

