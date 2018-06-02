<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $locationName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCampus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="campus")
     */
    private $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(string $locationName): self
    {
        $this->locationName = $locationName;

        return $this;
    }

    public function getIsCampus(): ?bool
    {
        return $this->isCampus;
    }

    public function setIsCampus(bool $isCampus): self
    {
        $this->isCampus = $isCampus;

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'locationName' => $this->locationName,
            'isCampus' => $this->isCampus
        );
    }

    public function __toString() {
        return (string) $this->locationName;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setOrigin($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getOrigin() === $this) {
                $user->setOrigin(null);
            }
        }

        return $this;
    }
}
