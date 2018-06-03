<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchRepository")
 */
class Match implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sharingUser;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $rentingUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSharingUser(): ?User
    {
        return $this->sharingUser;
    }

    public function setSharingUser(User $sharingUser): self
    {
        $this->sharingUser = $sharingUser;

        return $this;
    }

    public function getRentingUser(): ?User
    {
        return $this->rentingUser;
    }

    public function setRentingUser(User $rentingUser): self
    {
        $this->rentingUser = $rentingUser;

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'sharingUser' => $this->sharingUser,
            'rentingUser' => $this->rentingUser
        );
    }

    public function __toString()
    {
        return (string) $this->rentingUser.' & '.$this->sharingUser;
    }
}
