<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMVisaReqRepository")
 */
class CMVisaReq
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassenger", inversedBy="cMVisaReqs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $passenger;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $accepter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $rejecter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ARdes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCMoney")
     */
    private $moneyType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moneyValue;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $des;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea", inversedBy="cMVisaReqs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaState", inversedBy="cMVisaReqs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $visaState;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $buyer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buyDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaSendWay", inversedBy="cMVisaReqs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $waySendToCo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateReciveToCo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateSendToCo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaCountry")
     * @ORM\JoinColumn(nullable=false)
     */
    private $countryDes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="cMVisaReqs")
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="allvisaReq")
     */
    private $reciver;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $hsseAccepter;

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

    public function getAccepter(): ?SysPosition
    {
        return $this->accepter;
    }

    public function setAccepter(?SysPosition $accepter): self
    {
        $this->accepter = $accepter;

        return $this;
    }

    public function getRejecter(): ?SysPosition
    {
        return $this->rejecter;
    }

    public function setRejecter(?SysPosition $rejecter): self
    {
        $this->rejecter = $rejecter;

        return $this;
    }

    public function getARdes(): ?string
    {
        return $this->ARdes;
    }

    public function setARdes(?string $ARdes): self
    {
        $this->ARdes = $ARdes;

        return $this;
    }

    public function getMoneyType(): ?ACCMoney
    {
        return $this->moneyType;
    }

    public function setMoneyType(?ACCMoney $moneyType): self
    {
        $this->moneyType = $moneyType;

        return $this;
    }

    public function getMoneyValue(): ?string
    {
        return $this->moneyValue;
    }

    public function setMoneyValue(?string $moneyValue): self
    {
        $this->moneyValue = $moneyValue;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getArea(): ?SysArea
    {
        return $this->area;
    }

    public function setArea(?SysArea $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getVisaState(): ?CMVisaState
    {
        return $this->visaState;
    }

    public function setVisaState(?CMVisaState $visaState): self
    {
        $this->visaState = $visaState;

        return $this;
    }

    public function getBuyer(): ?SysPosition
    {
        return $this->buyer;
    }

    public function setBuyer(?SysPosition $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getBuyDate(): ?string
    {
        return $this->buyDate;
    }

    public function setBuyDate(?string $buyDate): self
    {
        $this->buyDate = $buyDate;

        return $this;
    }

    public function getWaySendToCo(): ?CMVisaSendWay
    {
        return $this->waySendToCo;
    }

    public function setWaySendToCo(?CMVisaSendWay $waySendToCo): self
    {
        $this->waySendToCo = $waySendToCo;

        return $this;
    }

    public function getDateReciveToCo(): ?string
    {
        return $this->DateReciveToCo;
    }

    public function setDateReciveToCo(?string $DateReciveToCo): self
    {
        $this->DateReciveToCo = $DateReciveToCo;

        return $this;
    }

    public function getDateSendToCo(): ?string
    {
        return $this->DateSendToCo;
    }

    public function setDateSendToCo(?string $DateSendToCo): self
    {
        $this->DateSendToCo = $DateSendToCo;

        return $this;
    }

    public function getCountryDes(): ?CMVisaCountry
    {
        return $this->countryDes;
    }

    public function setCountryDes(?CMVisaCountry $countryDes): self
    {
        $this->countryDes = $countryDes;

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

    public function getReciver(): ?SysPosition
    {
        return $this->reciver;
    }

    public function setReciver(?SysPosition $reciver): self
    {
        $this->reciver = $reciver;

        return $this;
    }

    public function getHsseAccepter(): ?SysPosition
    {
        return $this->hsseAccepter;
    }

    public function setHsseAccepter(?SysPosition $hsseAccepter): self
    {
        $this->hsseAccepter = $hsseAccepter;

        return $this;
    }
}
