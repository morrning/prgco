<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTRequestRepository")
 */
class ICTRequest
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
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $requestType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $machineID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $EMS;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $AcceptDoing;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seenTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seenID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acceptDoingTime;

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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getSubmitter(): ?string
    {
        return $this->submitter;
    }

    public function setSubmitter(string $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getRequestType(): ?string
    {
        return $this->requestType;
    }

    public function setRequestType(string $requestType): self
    {
        $this->requestType = $requestType;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

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

    public function getMachineID(): ?string
    {
        return $this->machineID;
    }

    public function setMachineID(?string $machineID): self
    {
        $this->machineID = $machineID;

        return $this;
    }

    public function getEMS(): ?string
    {
        return $this->EMS;
    }

    public function setEMS(?string $EMS): self
    {
        $this->EMS = $EMS;

        return $this;
    }

    public function getAcceptDoing(): ?string
    {
        return $this->AcceptDoing;
    }

    public function setAcceptDoing(?string $AccesptDoing): self
    {
        $this->AcceptDoing = $AccesptDoing;

        return $this;
    }

    public function getSeenTime(): ?string
    {
        return $this->seenTime;
    }

    public function setSeenTime(?string $seenTime): self
    {
        $this->seenTime = $seenTime;

        return $this;
    }

    public function getSeenID(): ?string
    {
        return $this->seenID;
    }

    public function setSeenID(?string $seenID): self
    {
        $this->seenID = $seenID;

        return $this;
    }

    public function getAcceptDoingTime(): ?string
    {
        return $this->acceptDoingTime;
    }

    public function setAcceptDoingTime(?string $acceptDoingTime): self
    {
        $this->acceptDoingTime = $acceptDoingTime;

        return $this;
    }
}
