<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysUserRepository")
 */
class SysUser
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
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uniqueID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobileNum;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SysPosition", mappedBy="userID")
     */
    private $sysPositions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="submitter", orphanRemoval=true)
     */
    private $cMVisaReqs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nationalCode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $suspend;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $employeNum;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SuuportTicket", mappedBy="submitter", orphanRemoval=true)
     */
    private $suuportTickets;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $contractor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HRMLetterOutCountry", mappedBy="user", orphanRemoval=true)
     */
    private $hRMLetterOutCountries;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $SocialMobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PMSusername;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PMSpassword;

    public function __construct()
    {
        $this->sysPositions = new ArrayCollection();
        $this->cMVisaReqs = new ArrayCollection();
        $this->suuportTickets = new ArrayCollection();
        $this->hRMLetterOutCountries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUniqueID(): ?string
    {
        return $this->uniqueID;
    }

    public function setUniqueID(string $uniqueID): self
    {
        $this->uniqueID = $uniqueID;

        return $this;
    }

    public function getMobileNum(): ?string
    {
        return $this->mobileNum;
    }

    public function setMobileNum(?string $mobileNum): self
    {
        $this->mobileNum = $mobileNum;

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
            $sysPosition->setUserID($this);
        }

        return $this;
    }

    public function removeSysPosition(SysPosition $sysPosition): self
    {
        if ($this->sysPositions->contains($sysPosition)) {
            $this->sysPositions->removeElement($sysPosition);
            // set the owning side to null (unless already changed)
            if ($sysPosition->getUserID() === $this) {
                $sysPosition->setUserID(null);
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

    public function getNationalCode(): ?string
    {
        return $this->nationalCode;
    }

    public function setNationalCode(string $nationalCode): self
    {
        $this->nationalCode = $nationalCode;

        return $this;
    }

    public function getSuspend(): ?bool
    {
        return $this->suspend;
    }

    public function setSuspend(?bool $suspend): self
    {
        $this->suspend = $suspend;

        return $this;
    }

    public function getEmployeNum(): ?string
    {
        return $this->employeNum;
    }

    public function setEmployeNum(string $employeNum): self
    {
        $this->employeNum = $employeNum;

        return $this;
    }

    /**
     * @return Collection|SuuportTicket[]
     */
    public function getSuuportTickets(): Collection
    {
        return $this->suuportTickets;
    }

    public function addSuuportTicket(SuuportTicket $suuportTicket): self
    {
        if (!$this->suuportTickets->contains($suuportTicket)) {
            $this->suuportTickets[] = $suuportTicket;
            $suuportTicket->setSubmitter($this);
        }

        return $this;
    }

    public function removeSuuportTicket(SuuportTicket $suuportTicket): self
    {
        if ($this->suuportTickets->contains($suuportTicket)) {
            $this->suuportTickets->removeElement($suuportTicket);
            // set the owning side to null (unless already changed)
            if ($suuportTicket->getSubmitter() === $this) {
                $suuportTicket->setSubmitter(null);
            }
        }

        return $this;
    }

    public function getContractor(): ?bool
    {
        return $this->contractor;
    }

    public function setContractor(?bool $contractor): self
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * @return Collection|HRMLetterOutCountry[]
     */
    public function getHRMLetterOutCountries(): Collection
    {
        return $this->hRMLetterOutCountries;
    }

    public function addHRMLetterOutCountry(HRMLetterOutCountry $hRMLetterOutCountry): self
    {
        if (!$this->hRMLetterOutCountries->contains($hRMLetterOutCountry)) {
            $this->hRMLetterOutCountries[] = $hRMLetterOutCountry;
            $hRMLetterOutCountry->setUser($this);
        }

        return $this;
    }

    public function removeHRMLetterOutCountry(HRMLetterOutCountry $hRMLetterOutCountry): self
    {
        if ($this->hRMLetterOutCountries->contains($hRMLetterOutCountry)) {
            $this->hRMLetterOutCountries->removeElement($hRMLetterOutCountry);
            // set the owning side to null (unless already changed)
            if ($hRMLetterOutCountry->getUser() === $this) {
                $hRMLetterOutCountry->setUser(null);
            }
        }

        return $this;
    }

    public function getSocialMobile(): ?string
    {
        return $this->SocialMobile;
    }

    public function setSocialMobile(?string $SocialMobile): self
    {
        $this->SocialMobile = $SocialMobile;

        return $this;
    }

    public function getPMSusername(): ?string
    {
        return $this->PMSusername;
    }

    public function setPMSusername(?string $PMSusername): self
    {
        $this->PMSusername = $PMSusername;

        return $this;
    }

    public function getPMSpassword(): ?string
    {
        return $this->PMSpassword;
    }

    public function setPMSpassword(?string $PMSpassword): self
    {
        $this->PMSpassword = $PMSpassword;

        return $this;
    }
}
