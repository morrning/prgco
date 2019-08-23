<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMPassengerRepository")
 */
class CMPassenger
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
    private $pname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pfamily;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pfather;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pbirthday;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pshenasname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pcodemeli;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lfamily;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lfather;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passNO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visaNO;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="cMPassengers")
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassengerType", inversedBy="cMPassengers")
     */
    private $ptype;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMAirTicket", mappedBy="passengerID")
     */
    private $cMAirTickets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMPassengerPersonalDoc", mappedBy="passenger", orphanRemoval=true)
     */
    private $cMPassengerPersonalDocs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMVisaReq", mappedBy="passenger", orphanRemoval=true)
     */
    private $cMVisaReqs;

    public function __construct()
    {
        $this->cMAirTickets = new ArrayCollection();
        $this->cMPassengerPersonalDocs = new ArrayCollection();
        $this->cMVisaReqs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPname(): ?string
    {
        return $this->pname;
    }

    public function setPname(string $pname): self
    {
        $this->pname = $pname;

        return $this;
    }

    public function getPfamily(): ?string
    {
        return $this->pfamily;
    }

    public function setPfamily(string $pfamily): self
    {
        $this->pfamily = $pfamily;

        return $this;
    }

    public function getPfather(): ?string
    {
        return $this->pfather;
    }

    public function setPfather(string $pfather): self
    {
        $this->pfather = $pfather;

        return $this;
    }

    public function getPbirthday(): ?string
    {
        return $this->pbirthday;
    }

    public function setPbirthday(string $pbirthday): self
    {
        $this->pbirthday = $pbirthday;

        return $this;
    }

    public function getPshenasname(): ?string
    {
        return $this->pshenasname;
    }

    public function setPshenasname(string $pshenasname): self
    {
        $this->pshenasname = $pshenasname;

        return $this;
    }

    public function getPcodemeli(): ?string
    {
        return $this->pcodemeli;
    }

    public function setPcodemeli(string $pcodemeli): self
    {
        $this->pcodemeli = $pcodemeli;

        return $this;
    }

    public function getLname(): ?string
    {
        return $this->lname;
    }

    public function setLname(?string $lname): self
    {
        $this->lname = $lname;

        return $this;
    }

    public function getLfamily(): ?string
    {
        return $this->lfamily;
    }

    public function setLfamily(?string $lfamily): self
    {
        $this->lfamily = $lfamily;

        return $this;
    }

    public function getLfather(): ?string
    {
        return $this->lfather;
    }

    public function setLfather(?string $lfather): self
    {
        $this->lfather = $lfather;

        return $this;
    }

    public function getPassNO(): ?string
    {
        return $this->passNO;
    }

    public function setPassNO(?string $passNO): self
    {
        $this->passNO = $passNO;

        return $this;
    }

    public function getVisaNO(): ?string
    {
        return $this->visaNO;
    }

    public function setVisaNO(?string $visaNO): self
    {
        $this->visaNO = $visaNO;

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

    public function getPtype(): ?CMPassengerType
    {
        return $this->ptype;
    }

    public function setPtype(?CMPassengerType $ptype): self
    {
        $this->ptype = $ptype;

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
            $cMAirTicket->setPassengerID($this);
        }

        return $this;
    }

    public function removeCMAirTicket(CMAirTicket $cMAirTicket): self
    {
        if ($this->cMAirTickets->contains($cMAirTicket)) {
            $this->cMAirTickets->removeElement($cMAirTicket);
            // set the owning side to null (unless already changed)
            if ($cMAirTicket->getPassengerID() === $this) {
                $cMAirTicket->setPassengerID(null);
            }
        }

        return $this;
    }

    public function getAdr(): ?string
    {
        return $this->adr;
    }

    public function setAdr(?string $adr): self
    {
        $this->adr = $adr;

        return $this;
    }

    public function getTel1(): ?string
    {
        return $this->tel1;
    }

    public function setTel1(?string $tel1): self
    {
        $this->tel1 = $tel1;

        return $this;
    }

    public function getTel2(): ?string
    {
        return $this->tel2;
    }

    public function setTel2(?string $tel2): self
    {
        $this->tel2 = $tel2;

        return $this;
    }

    /**
     * @return Collection|CMPassengerPersonalDoc[]
     */
    public function getCMPassengerPersonalDocs(): Collection
    {
        return $this->cMPassengerPersonalDocs;
    }

    public function addCMPassengerPersonalDoc(CMPassengerPersonalDoc $cMPassengerPersonalDoc): self
    {
        if (!$this->cMPassengerPersonalDocs->contains($cMPassengerPersonalDoc)) {
            $this->cMPassengerPersonalDocs[] = $cMPassengerPersonalDoc;
            $cMPassengerPersonalDoc->setPassenger($this);
        }

        return $this;
    }

    public function removeCMPassengerPersonalDoc(CMPassengerPersonalDoc $cMPassengerPersonalDoc): self
    {
        if ($this->cMPassengerPersonalDocs->contains($cMPassengerPersonalDoc)) {
            $this->cMPassengerPersonalDocs->removeElement($cMPassengerPersonalDoc);
            // set the owning side to null (unless already changed)
            if ($cMPassengerPersonalDoc->getPassenger() === $this) {
                $cMPassengerPersonalDoc->setPassenger(null);
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
            $cMVisaReq->setPassenger($this);
        }

        return $this;
    }

    public function removeCMVisaReq(CMVisaReq $cMVisaReq): self
    {
        if ($this->cMVisaReqs->contains($cMVisaReq)) {
            $this->cMVisaReqs->removeElement($cMVisaReq);
            // set the owning side to null (unless already changed)
            if ($cMVisaReq->getPassenger() === $this) {
                $cMVisaReq->setPassenger(null);
            }
        }

        return $this;
    }
}
