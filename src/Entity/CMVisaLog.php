<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMVisaLogRepository")
 */
class CMVisaLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassenger", inversedBy="cMVisaLogs")
     */
    private $passenger;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaCountry")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $visaType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateStart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateEnd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaReq")
     */
    private $requestID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassenger(): ?CMPassenger
    {
        return $this->passenger;
    }

    public function setPassenger(?CMPassenger $passenger): self
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getCountry(): ?CMVisaCountry
    {
        return $this->country;
    }

    public function setCountry(?CMVisaCountry $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getVisaType(): ?CMVisaType
    {
        return $this->visaType;
    }

    public function setVisaType(?CMVisaType $visaType): self
    {
        $this->visaType = $visaType;

        return $this;
    }

    public function getDateStart(): ?string
    {
        return $this->DateStart;
    }

    public function setDateStart(?string $DateStart): self
    {
        $this->DateStart = $DateStart;

        return $this;
    }

    public function getDateEnd(): ?string
    {
        return $this->DateEnd;
    }

    public function setDateEnd(?string $DateEnd): self
    {
        $this->DateEnd = $DateEnd;

        return $this;
    }

    public function getRequestID(): ?CMVisaReq
    {
        return $this->requestID;
    }

    public function setRequestID(?CMVisaReq $requestID): self
    {
        $this->requestID = $requestID;

        return $this;
    }
}
