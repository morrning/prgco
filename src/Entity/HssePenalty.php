<?php

namespace App\Entity;

use App\Repository\HssePenaltyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HssePenaltyRepository::class)
 */
class HssePenalty
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SysPosition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $submiter;

    /**
     * @ORM\Column(type="bigint")
     */
    private $dateSubmit;

    /**
     * @ORM\ManyToOne(targetEntity=CMPassenger::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $Passenger;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $work;

    /**
     * @ORM\ManyToOne(targetEntity=SysArea::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $penaltyType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $place;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getPassenger(): ?CMPassenger
    {
        return $this->Passenger;
    }

    public function setPassenger(?CMPassenger $Passenger): self
    {
        $this->Passenger = $Passenger;

        return $this;
    }

    public function getWork(): ?string
    {
        return $this->work;
    }

    public function setWork(?string $work): self
    {
        $this->work = $work;

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

    public function getPenaltyType(): ?string
    {
        return $this->penaltyType;
    }

    public function setPenaltyType(string $penaltyType): self
    {
        $this->penaltyType = $penaltyType;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
