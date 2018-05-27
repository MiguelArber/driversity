<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeFlex;

    /**
     * @ORM\Column(type="integer")
     */
    private $locationFlex;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location", cascade={"persist", "remove"})
     */
    private $origin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Vehicle", cascade={"persist", "remove"})
     */
    private $vehicle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="user", orphanRemoval=true)
     */
    private $schedule;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Location", cascade={"persist", "remove"})
     */
    private $campus;

    public function __construct()
    {
        $this->schedule = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTimeFlex(): ?int
    {
        return $this->timeFlex;
    }

    public function setTimeFlex(int $timeFlex): self
    {
        $this->timeFlex = $timeFlex;

        return $this;
    }

    public function getLocationFlex(): ?int
    {
        return $this->locationFlex;
    }

    public function setLocationFlex(int $locationFlex): self
    {
        $this->locationFlex = $locationFlex;

        return $this;
    }

    public function getOrigin(): ?Location
    {
        return $this->origin;
    }

    public function setOrigin(?Location $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getCampus(): ?Location
    {
        return $this->campus;
    }

    public function setCampus(?Location $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedule(): Collection
    {
        return $this->schedule;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedule->contains($schedule)) {
            $this->schedule[] = $schedule;
            $schedule->setUser($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedule->contains($schedule)) {
            $this->schedule->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getUser() === $this) {
                $schedule->setUser(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->username;
    }
}
