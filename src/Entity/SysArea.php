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

    public function __construct()
    {
        $this->sysPositions = new ArrayCollection();
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
}
