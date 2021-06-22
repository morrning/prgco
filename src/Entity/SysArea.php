<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysAreaRepository")
 */
class SysArea
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
    private $areaName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $des;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Project", mappedBy="areaID", cascade={"persist", "remove"})
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SysPosition", mappedBy="defaultArea")
     */
    private $sysPositions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMAirTicket", mappedBy="Area")
     */
    private $cMAirTickets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="area", orphanRemoval=true)
     */
    private $cMVisaReqs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HotelingHotel", mappedBy="area", orphanRemoval=true)
     */
    private $hotelingHotels;

    public function __construct()
    {
        $this->sysPositions = new ArrayCollection();
        $this->cMAirTickets = new ArrayCollection();
        $this->cMVisaReqs = new ArrayCollection();
        $this->hotelingHotels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaName(): ?string
    {
        return $this->areaName;
    }

    public function setAreaName(string $areaName): self
    {
        $this->areaName = $areaName;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        // set the owning side of the relation if necessary
        if ($this !== $project->getAreaID()) {
            $project->setAreaID($this);
        }

        return $this;
    }

    /**
     * @return Collection|SysPosition[]
     */
    public function getSysPositions(): Collection
    {
        return $this->sysPositions;
    }

    public function addSysPosition(SysPosition $sysPosition): self
    {
        if (!$this->sysPositions->contains($sysPosition)) {
            $this->sysPositions[] = $sysPosition;
            $sysPosition->setDefaultArea($this);
        }

        return $this;
    }

    public function removeSysPosition(SysPosition $sysPosition): self
    {
        if ($this->sysPositions->contains($sysPosition)) {
            $this->sysPositions->removeElement($sysPosition);
            // set the owning side to null (unless already changed)
            if ($sysPosition->getDefaultArea() === $this) {
                $sysPosition->setDefaultArea(null);
            }
        }

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
            $cMAirTicket->setArea($this);
        }

        return $this;
    }

    public function removeCMAirTicket(CMAirTicket $cMAirTicket): self
    {
        if ($this->cMAirTickets->contains($cMAirTicket)) {
            $this->cMAirTickets->removeElement($cMAirTicket);
            // set the owning side to null (unless already changed)
            if ($cMAirTicket->getArea() === $this) {
                $cMAirTicket->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMVisaReq[]
     */
    public function getCMVisaReqs(): Collection
    {
        return $this->cMVisaReqs;
    }

    public function addCMVisaReq(CMVisaReq $cMVisaReq): self
    {
        if (!$this->cMVisaReqs->contains($cMVisaReq)) {
            $this->cMVisaReqs[] = $cMVisaReq;
            $cMVisaReq->setArea($this);
        }

        return $this;
    }

    public function removeCMVisaReq(CMVisaReq $cMVisaReq): self
    {
        if ($this->cMVisaReqs->contains($cMVisaReq)) {
            $this->cMVisaReqs->removeElement($cMVisaReq);
            // set the owning side to null (unless already changed)
            if ($cMVisaReq->getArea() === $this) {
                $cMVisaReq->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|HotelingHotel[]
     */
    public function getHotelingHotels(): Collection
    {
        return $this->hotelingHotels;
    }

    public function addHotelingHotel(HotelingHotel $hotelingHotel): self
    {
        if (!$this->hotelingHotels->contains($hotelingHotel)) {
            $this->hotelingHotels[] = $hotelingHotel;
            $hotelingHotel->setArea($this);
        }

        return $this;
    }

    public function removeHotelingHotel(HotelingHotel $hotelingHotel): self
    {
        if ($this->hotelingHotels->contains($hotelingHotel)) {
            $this->hotelingHotels->removeElement($hotelingHotel);
            // set the owning side to null (unless already changed)
            if ($hotelingHotel->getArea() === $this) {
                $hotelingHotel->setArea(null);
            }
        }

        return $this;
    }
}
