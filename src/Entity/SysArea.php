<?php

namespace App\Entity;

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
}
