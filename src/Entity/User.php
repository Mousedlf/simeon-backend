<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'users:read', 'trip:read', 'expense:new'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read','users:read', 'trip:read','invites:read', 'expense:new', 'expense:index'])]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $public = null;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $createdTrips;

    /**
     * @var Collection<int, TripInvite>
     */
    #[ORM\OneToMany(targetEntity: TripInvite::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $sentTripInvites;

    /**
     * @var Collection<int, TripInvite>
     */
    #[ORM\OneToMany(targetEntity: TripInvite::class, mappedBy: 'recipient', orphanRemoval: true)]
    private Collection $receivedTripInvites;

    /**
     * @var Collection<int, TripParticipant>
     */
    #[ORM\OneToMany(targetEntity: TripParticipant::class, mappedBy: 'participant', orphanRemoval: true)]
    private Collection $trips;

    public function __construct()
    {
        $this->createdTrips = new ArrayCollection();
        $this->sentTripInvites = new ArrayCollection();
        $this->receivedTripInvites = new ArrayCollection();
        $this->trips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getCreatedTrips(): Collection
    {
        return $this->createdTrips;
    }

    public function addCreatedTrip(Trip $trip): static
    {
        if (!$this->createdTrips->contains($trip)) {
            $this->createdTrips->add($trip);
            $trip->setOwner($this);
        }

        return $this;
    }

    public function removeCreatedTrip(Trip $trip): static
    {
        if ($this->createdTrips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getOwner() === $this) {
                $trip->setOwner(null);
            }
        }

        return $this;
    }

   /**
     * @return Collection<int, TripInvite>
     */
    public function getSentTripInvites(): Collection
    {
        return $this->sentTripInvites;
    }

    public function addSentTripInvite(TripInvite $sentTripInvite): static
    {
        if (!$this->sentTripInvites->contains($sentTripInvite)) {
            $this->sentTripInvites->add($sentTripInvite);
            $sentTripInvite->setSender($this);
        }

        return $this;
    }

    public function removeSentTripInvite(TripInvite $sentTripInvite): static
    {
        if ($this->sentTripInvites->removeElement($sentTripInvite)) {
            // set the owning side to null (unless already changed)
            if ($sentTripInvite->getSender() === $this) {
                $sentTripInvite->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TripInvite>
     */
    public function getReceivedTripInvites(): Collection
    {
        return $this->receivedTripInvites;
    }

    public function addReceivedTripInvite(TripInvite $receivedTripInvite): static
    {
        if (!$this->receivedTripInvites->contains($receivedTripInvite)) {
            $this->receivedTripInvites->add($receivedTripInvite);
            $receivedTripInvite->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedTripInvite(TripInvite $receivedTripInvite): static
    {
        if ($this->receivedTripInvites->removeElement($receivedTripInvite)) {
            // set the owning side to null (unless already changed)
            if ($receivedTripInvite->getRecipient() === $this) {
                $receivedTripInvite->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TripParticipant>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(TripParticipant $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setParticipant($this);
        }

        return $this;
    }

    public function removeTrip(TripParticipant $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getParticipant() === $this) {
                $trip->setParticipant(null);
            }
        }

        return $this;
    }
}
