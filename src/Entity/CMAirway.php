<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMAirwayRepository")
 */
class CMAirway
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
    private $airwayName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMAirTicket", mappedBy="FlyAirway")
     */
    private $cMAirTickets;

    public function __construct()
    {
        $this->cMAirTickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAirwayName(): ?string
    {
        return $this->airwayName;
    }

    public function setAirwayName(string $airwayName): self
    {
        $this->airwayName = $airwayName;

        return $this;
    }

    /**
     * @return Collection|CMAirTicket[]
     */
    public function getCMAirTickets(): Collection
    {
        return $this->cMAirTickets;
    }

    public function addCMAirTicket(CMAirTicket $cMAirTicket): self
    {
        if (!$this->cMAirTickets->contains($cMAirTicket)) {
            $this->cMAirTickets[] = $cMAirTicket;
            $cMAirTicket->setFlyAirway($this);
        }

        return $this;
    }

    public function removeCMAirTicket(CMAirTicket $cMAirTicket): self
    {
        if ($this->cMAirTickets->contains($cMAirTicket)) {
            $this->cMAirTickets->removeElement($cMAirTicket);
            // set the owning side to null (unless already changed)
            if ($cMAirTicket->getFlyAirway() === $this) {
                $cMAirTicket->setFlyAirway(null);
            }
        }

        return $this;
    }
}
