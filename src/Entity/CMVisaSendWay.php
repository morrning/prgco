<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMVisaSendWayRepository")
 */
class CMVisaSendWay
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
    private $WayName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $WayCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="waySendToCo", orphanRemoval=true)
     */
    private $cMVisaReqs;

    public function __construct()
    {
        $this->cMVisaReqs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWayName(): ?string
    {
        return $this->WayName;
    }

    public function setWayName(string $WayName): self
    {
        $this->WayName = $WayName;

        return $this;
    }

    public function getWayCode(): ?string
    {
        return $this->WayCode;
    }

    public function setWayCode(string $WayCode): self
    {
        $this->WayCode = $WayCode;

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
            $cMVisaReq->setWaySendToCo($this);
        }

        return $this;
    }

    public function removeCMVisaReq(CMVisaReq $cMVisaReq): self
    {
        if ($this->cMVisaReqs->contains($cMVisaReq)) {
            $this->cMVisaReqs->removeElement($cMVisaReq);
            // set the owning side to null (unless already changed)
            if ($cMVisaReq->getWaySendToCo() === $this) {
                $cMVisaReq->setWaySendToCo(null);
            }
        }

        return $this;
    }
}
