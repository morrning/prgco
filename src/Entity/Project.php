<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
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
    private $areaID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Pprogress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Cprogress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaID(): ?string
    {
        return $this->areaID;
    }

    public function setAreaID(string $areaID): self
    {
        $this->areaID = $areaID;

        return $this;
    }

    public function getLastUpdate(): ?string
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(string $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getPprogress(): ?string
    {
        return $this->Pprogress;
    }

    public function setPprogress(?string $Pprogress): self
    {
        $this->Pprogress = $Pprogress;

        return $this;
    }

    public function getCprogress(): ?string
    {
        return $this->Cprogress;
    }

    public function setCprogress(?string $Cprogress): self
    {
        $this->Cprogress = $Cprogress;

        return $this;
    }
}
