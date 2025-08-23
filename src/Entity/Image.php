<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[Vich\Uploadable]
class Image
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'trips', fileNameProperty: 'image.name', size: 'image.size')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private ?EmbeddedFile $image = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['trip:read', 'day:read', 'day:index', 'activity:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'image', cascade: ['persist', 'remove'])]
    private ?Trip $trip = null;

    #[ORM\OneToOne(mappedBy: 'image', cascade: ['persist', 'remove'])]
    private ?TripActivity $tripActivity = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Groups(['trip:read', 'day:read', 'day:index', 'activity:read'])]
    private ?string $googleImageUrl = null;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        // unset the owning side of the relation if necessary
        if ($trip === null && $this->trip !== null) {
            $this->trip->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($trip !== null && $trip->getImage() !== $this) {
            $trip->setImage($this);
        }

        $this->trip = $trip;

        return $this;
    }

    #[Groups(['trip:read', 'day:read', 'day:index', 'activity:read'])]
    public function getImageUrl(): ?string
    {
        return $this->image?->getName() ? '/images/trips/' . $this->image->getName() : null;
    }

    public function getTripActivity(): ?TripActivity
    {
        return $this->tripActivity;
    }

    public function setTripActivity(?TripActivity $tripActivity): static
    {
        // unset the owning side of the relation if necessary
        if ($tripActivity === null && $this->tripActivity !== null) {
            $this->tripActivity->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($tripActivity !== null && $tripActivity->getImage() !== $this) {
            $tripActivity->setImage($this);
        }

        $this->tripActivity = $tripActivity;

        return $this;
    }

    public function getGoogleImageUrl(): ?string
    {
        return $this->googleImageUrl;
    }

    public function setGoogleImageUrl(?string $googleImageUrl): static
    {
        $this->googleImageUrl = $googleImageUrl;

        return $this;
    }
}