<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $dateSubmit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $AcceptDoing;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seenTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea")
     */
    private $areaID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ICTRequestType", inversedBy="iCTRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requestType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ICTRequestState", inversedBy="iCTRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ICTRequestEMSState")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ems;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $seenID;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ICTDoing", mappedBy="reqID", orphanRemoval=true)
     */
    private $iCTDoings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acceptDoingTime;

    public function __construct()
    {
        $this->iCTDoings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDateSubmit()
    {
        return $this->dateSubmit;
    }

    /**
     * @param mixed $dateSubmit
     */
    public function setDateSubmit($dateSubmit): void
    {
        $this->dateSubmit = $dateSubmit;
    }

    /**
     * @return mixed
     */
    public function getDes()
    {
        return $this->des;
    }

    /**
     * @param mixed $des
     */
    public function setDes($des): void
    {
        $this->des = $des;
    }

    /**
     * @return mixed
     */
    public function getAcceptDoing()
    {
        return $this->AcceptDoing;
    }

    /**
     * @param mixed $AcceptDoing
     */
    public function setAcceptDoing($AcceptDoing): void
    {
        $this->AcceptDoing = $AcceptDoing;
    }

    /**
     * @return mixed
     */
    public function getSeenTime()
    {
        return $this->seenTime;
    }

    /**
     * @param mixed $seenTime
     */
    public function setSeenTime($seenTime): void
    {
        $this->seenTime = $seenTime;
    }

    /**
     * @return mixed
     */
    public function getAreaID()
    {
        return $this->areaID;
    }

    /**
     * @param mixed $areaID
     */
    public function setAreaID($areaID): void
    {
        $this->areaID = $areaID;
    }

    public function getSubmitter(): ?SysPosition
    {
        return $this->submitter;
    }

    public function setSubmitter(?SysPosition $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getRequestType(): ?ICTRequestType
    {
        return $this->requestType;
    }

    public function setRequestType(?ICTRequestType $requestType): self
    {
        $this->requestType = $requestType;

        return $this;
    }

    public function getState(): ?ICTRequestState
    {
        return $this->state;
    }

    public function setState(?ICTRequestState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getEms(): ?ICTRequestEMSState
    {
        return $this->ems;
    }

    public function setEms(?ICTRequestEMSState $ems): self
    {
        $this->ems = $ems;

        return $this;
    }

    public function getSeenID(): ?SysPosition
    {
        return $this->seenID;
    }

    public function setSeenID(?SysPosition $seenID): self
    {
        $this->seenID = $seenID;

        return $this;
    }

    /**
     * @return Collection|ICTDoing[]
     */
    public function getICTDoings(): Collection
    {
        return $this->iCTDoings;
    }

    public function addICTDoing(ICTDoing $iCTDoing): self
    {
        if (!$this->iCTDoings->contains($iCTDoing)) {
            $this->iCTDoings[] = $iCTDoing;
            $iCTDoing->setReqID($this);
        }

        return $this;
    }

    public function removeICTDoing(ICTDoing $iCTDoing): self
    {
        if ($this->iCTDoings->contains($iCTDoing)) {
            $this->iCTDoings->removeElement($iCTDoing);
            // set the owning side to null (unless already changed)
            if ($iCTDoing->getReqID() === $this) {
                $iCTDoing->setReqID(null);
            }
        }

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
