<?php

namespace App\Entity;

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
    private $ptype;

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
}
