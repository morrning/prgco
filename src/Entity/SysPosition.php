<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysPositionRepository")
 */
class SysPosition
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
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upperID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicLabel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $permissiongroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NewsPost", mappedBy="submitter")
     */
    private $newsPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMPassenger", mappedBy="submitter")
     */
    private $cMPassengers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser", inversedBy="sysPositions")
     */
    private $userID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea", inversedBy="sysPositions")
     */
    private $defaultArea;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SysNotification", mappedBy="userID", orphanRemoval=true)
     */
    private $sysNotifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMAirTicket", mappedBy="accepter")
     */
    private $cMAirTickets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMAirTicket", mappedBy="submitter", orphanRemoval=true)
     */
    private $no;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ACCdoc", mappedBy="icuser")
     */
    private $aCCdocs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="submitter")
     */
    private $cMVisaReqs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="reciver")
     */
    private $allvisaReq;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="hseAR")
     */
    private $visaReqHse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysRoll")
     */
    private $roll;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permissionFromRoll;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $constractor;

    public function __construct()
    {
        $this->newsPosts = new ArrayCollection();
        $this->cMPassengers = new ArrayCollection();
        $this->sysNotifications = new ArrayCollection();
        $this->cMAirTickets = new ArrayCollection();
        $this->no = new ArrayCollection();
        $this->aCCdocs = new ArrayCollection();
        $this->cMVisaReqs = new ArrayCollection();
        $this->allvisaReq = new ArrayCollection();
        $this->visaReqHse = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getUpperID()
    {
        return $this->upperID;
    }

    /**
     * @param mixed $upperID
     */
    public function setUpperID($upperID): void
    {
        $this->upperID = $upperID;
    }

    /**
     * @return mixed
     */
    public function getPublicLabel()
    {
        return $this->publicLabel;
    }

    /**
     * @param mixed $publicLabel
     */
    public function setPublicLabel($publicLabel): void
    {
        $this->publicLabel = $publicLabel;
    }

    /**
     * @return mixed
     */
    public function getisDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return mixed
     */
    public function getPermissiongroups()
    {
        return $this->permissiongroups;
    }

    /**
     * @param mixed $permissiongroups
     */
    public function setPermissiongroups($permissiongroups): void
    {
        $this->permissiongroups = $permissiongroups;
    }



    /**
     * @return Collection|NewsPost[]
     */
    public function getNewsPosts(): Collection
    {
        return $this->newsPosts;
    }

    public function addNewsPost(NewsPost $newsPost): self
    {
        if (!$this->newsPosts->contains($newsPost)) {
            $this->newsPosts[] = $newsPost;
            $newsPost->setSubmitter($this);
        }

        return $this;
    }

    public function removeNewsPost(NewsPost $newsPost): self
    {
        if ($this->newsPosts->contains($newsPost)) {
            $this->newsPosts->removeElement($newsPost);
            // set the owning side to null (unless already changed)
            if ($newsPost->getSubmitter() === $this) {
                $newsPost->setSubmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMPassenger[]
     */
    public function getCMPassengers(): Collection
    {
        return $this->cMPassengers;
    }

    public function addCMPassenger(CMPassenger $cMPassenger): self
    {
        if (!$this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers[] = $cMPassenger;
            $cMPassenger->setSubmitter($this);
        }

        return $this;
    }

    public function removeCMPassenger(CMPassenger $cMPassenger): self
    {
        if ($this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers->removeElement($cMPassenger);
            // set the owning side to null (unless already changed)
            if ($cMPassenger->getSubmitter() === $this) {
                $cMPassenger->setSubmitter(null);
            }
        }

        return $this;
    }

    public function getUserID(): ?SysUser
    {
        return $this->userID;
    }

    public function setUserID(?SysUser $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getDefaultArea(): ?SysArea
    {
        return $this->defaultArea;
    }

    public function setDefaultArea(?SysArea $defaultArea): self
    {
        $this->defaultArea = $defaultArea;

        return $this;
    }

    /**
     * @return Collection|SysNotification[]
     */
    public function getSysNotifications(): Collection
    {
        return $this->sysNotifications;
    }

    public function addSysNotification(SysNotification $sysNotification): self
    {
        if (!$this->sysNotifications->contains($sysNotification)) {
            $this->sysNotifications[] = $sysNotification;
            $sysNotification->setUserID($this);
        }

        return $this;
    }

    public function removeSysNotification(SysNotification $sysNotification): self
    {
        if ($this->sysNotifications->contains($sysNotification)) {
            $this->sysNotifications->removeElement($sysNotification);
            // set the owning side to null (unless already changed)
            if ($sysNotification->getUserID() === $this) {
                $sysNotification->setUserID(null);
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
            $cMAirTicket->setAccepter($this);
        }

        return $this;
    }

    public function removeCMAirTicket(CMAirTicket $cMAirTicket): self
    {
        if ($this->cMAirTickets->contains($cMAirTicket)) {
            $this->cMAirTickets->removeElement($cMAirTicket);
            // set the owning side to null (unless already changed)
            if ($cMAirTicket->getAccepter() === $this) {
                $cMAirTicket->setAccepter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMAirTicket[]
     */
    public function getNo(): Collection
    {
        return $this->no;
    }

    public function addNo(CMAirTicket $no): self
    {
        if (!$this->no->contains($no)) {
            $this->no[] = $no;
            $no->setSubmitter($this);
        }

        return $this;
    }

    public function removeNo(CMAirTicket $no): self
    {
        if ($this->no->contains($no)) {
            $this->no->removeElement($no);
            // set the owning side to null (unless already changed)
            if ($no->getSubmitter() === $this) {
                $no->setSubmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ACCdoc[]
     */
    public function getACCdocs(): Collection
    {
        return $this->aCCdocs;
    }

    public function addACCdoc(ACCdoc $aCCdoc): self
    {
        if (!$this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs[] = $aCCdoc;
            $aCCdoc->setIcuser($this);
        }

        return $this;
    }

    public function removeACCdoc(ACCdoc $aCCdoc): self
    {
        if ($this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs->removeElement($aCCdoc);
            // set the owning side to null (unless already changed)
            if ($aCCdoc->getIcuser() === $this) {
                $aCCdoc->setIcuser(null);
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
            $cMVisaReq->setSubmitter($this);
        }

        return $this;
    }

    public function removeCMVisaReq(CMVisaReq $cMVisaReq): self
    {
        if ($this->cMVisaReqs->contains($cMVisaReq)) {
            $this->cMVisaReqs->removeElement($cMVisaReq);
            // set the owning side to null (unless already changed)
            if ($cMVisaReq->getSubmitter() === $this) {
                $cMVisaReq->setSubmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMVisaReq[]
     */
    public function getAllvisaReq(): Collection
    {
        return $this->allvisaReq;
    }

    public function addAllvisaReq(CMVisaReq $allvisaReq): self
    {
        if (!$this->allvisaReq->contains($allvisaReq)) {
            $this->allvisaReq[] = $allvisaReq;
            $allvisaReq->setReciver($this);
        }

        return $this;
    }

    public function removeAllvisaReq(CMVisaReq $allvisaReq): self
    {
        if ($this->allvisaReq->contains($allvisaReq)) {
            $this->allvisaReq->removeElement($allvisaReq);
            // set the owning side to null (unless already changed)
            if ($allvisaReq->getReciver() === $this) {
                $allvisaReq->setReciver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMVisaReq[]
     */
    public function getVisaReqHse(): Collection
    {
        return $this->visaReqHse;
    }

    public function addVisaReqHse(CMVisaReq $visaReqHse): self
    {
        if (!$this->visaReqHse->contains($visaReqHse)) {
            $this->visaReqHse[] = $visaReqHse;
            $visaReqHse->setHseAR($this);
        }

        return $this;
    }

    public function removeVisaReqHse(CMVisaReq $visaReqHse): self
    {
        if ($this->visaReqHse->contains($visaReqHse)) {
            $this->visaReqHse->removeElement($visaReqHse);
            // set the owning side to null (unless already changed)
            if ($visaReqHse->getHseAR() === $this) {
                $visaReqHse->setHseAR(null);
            }
        }

        return $this;
    }

    public function getRoll(): ?SysRoll
    {
        return $this->roll;
    }

    public function setRoll(?SysRoll $roll): self
    {
        $this->roll = $roll;

        return $this;
    }

    public function getPermissionFromRoll(): ?bool
    {
        return $this->permissionFromRoll;
    }

    public function setPermissionFromRoll(?bool $permissionFromRoll): self
    {
        $this->permissionFromRoll = $permissionFromRoll;

        return $this;
    }

    public function getConstractor(): ?bool
    {
        return $this->constractor;
    }

    public function setConstractor(?bool $constractor): self
    {
        $this->constractor = $constractor;

        return $this;
    }

}