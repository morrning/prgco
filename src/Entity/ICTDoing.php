<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTDoingRepository")
 */
class ICTDoing
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ICTRequest", inversedBy="iCTDoings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reqID;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
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

    public function getReqID(): ?ICTRequest
    {
        return $this->reqID;
    }

    public function setReqID(?ICTRequest $reqID): self
    {
        $this->reqID = $reqID;

        return $this;
    }
    
}
