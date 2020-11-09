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
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaCountry")
     * @ORM\JoinColumn(nullable=false)
     */
    private $countryDes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="cMVisaReqs")
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMList")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmlist;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaState")
     */
    private $visaState;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ADDateSubmit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMVisaType")
     */
    private $VisaType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateStart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DateEnd;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAccepter()
    {
        return $this->accepter;
    }

    /**
     * @param mixed $accepter
     */
    public function setAccepter($accepter): void
    {
        $this->accepter = $accepter;
    }

    /**
     * @return mixed
     */
    public function getRejecter()
    {
        return $this->rejecter;
    }

    /**
     * @param mixed $rejecter
     */
    public function setRejecter($rejecter): void
    {
        $this->rejecter = $rejecter;
    }

    /**
     * @return mixed
     */
    public function getARdes()
    {
        return $this->ARdes;
    }

    /**
     * @param mixed $ARdes
     */
    public function setARdes($ARdes): void
    {
        $this->ARdes = $ARdes;
    }

    /**
     * @return mixed
     */
    public function getMoneyType()
    {
        return $this->moneyType;
    }

    /**
     * @param mixed $moneyType
     */
    public function setMoneyType($moneyType): void
    {
        $this->moneyType = $moneyType;
    }

    /**
     * @return mixed
     */
    public function getMoneyValue()
    {
        return $this->moneyValue;
    }

    /**
     * @param mixed $moneyValue
     */
    public function setMoneyValue($moneyValue): void
    {
        $this->moneyValue = $moneyValue;
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
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area): void
    {
        $this->area = $area;
    }

    /**
     * @return mixed
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @param mixed $buyer
     */
    public function setBuyer($buyer): void
    {
        $this->buyer = $buyer;
    }

    /**
     * @return mixed
     */
    public function getBuyDate()
    {
        return $this->buyDate;
    }

    /**
     * @param mixed $buyDate
     */
    public function setBuyDate($buyDate): void
    {
        $this->buyDate = $buyDate;
    }

    /**
     * @return mixed
     */
    public function getWaySendToCo()
    {
        return $this->waySendToCo;
    }

    /**
     * @param mixed $waySendToCo
     */
    public function setWaySendToCo($waySendToCo): void
    {
        $this->waySendToCo = $waySendToCo;
    }

    /**
     * @return mixed
     */
    public function getCountryDes()
    {
        return $this->countryDes;
    }

    /**
     * @param mixed $countryDes
     */
    public function setCountryDes($countryDes): void
    {
        $this->countryDes = $countryDes;
    }

    /**
     * @return mixed
     */
    public function getSubmitter()
    {
        return $this->submitter;
    }

    /**
     * @param mixed $submitter
     */
    public function setSubmitter($submitter): void
    {
        $this->submitter = $submitter;
    }

    /**
     * @return mixed
     */
    public function getCmlist()
    {
        return $this->cmlist;
    }

    /**
     * @param mixed $cmlist
     */
    public function setCmlist($cmlist): void
    {
        $this->cmlist = $cmlist;
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

    public function getADDateSubmit(): ?string
    {
        return $this->ADDateSubmit;
    }

    public function setADDateSubmit(?string $ADDateSubmit): self
    {
        $this->ADDateSubmit = $ADDateSubmit;

        return $this;
    }

    public function getVisaType(): ?CMVisaType
    {
        return $this->VisaType;
    }

    public function setVisaType(?CMVisaType $VisaType): self
    {
        $this->VisaType = $VisaType;

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


}
