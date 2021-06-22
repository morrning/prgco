<?php

namespace App\Entity;

use App\Repository\HotelingPassengerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HotelingPassengerRepository::class)
 */
class HotelingPassenger
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=HotelingRoom::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @ORM\ManyToOne(targetEntity=CMPassenger::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $passenger;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $dateSubmit;

    /**
     * @ORM\ManyToOne(targetEntity=SysArea::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity=SysPosition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $submiter;

    /**
     * @ORM\ManyToOne(targetEntity=HotelingHotel::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $hotel;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $day;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?HotelingRoom
    {
        return $this->room;
    }

    public function setRoom(?HotelingRoom $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getPassenger(): ?CMPassenger
    {
        return $this->passenger;
    }

    public function setPassenger(?CMPassenger $passenger): self
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getArea(): ?SysArea
    {
        return $this->area;
    }

    public function setArea(?SysArea $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getSubmiter(): ?SysPosition
    {
        return $this->submiter;
    }

    public function setSubmiter(?SysPosition $submiter): self
    {
        $this->submiter = $submiter;

        return $this;
    }

    public function getHotel(): ?HotelingHotel
    {
        return $this->hotel;
    }

    public function setHotel(?HotelingHotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }
}
