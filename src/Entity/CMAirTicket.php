<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMAirTicketRepository")
 */
class CMAirTicket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMCities")
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMCities")
     */
    private $destination;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="cMAirTickets")
     */
    private $accepter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moneyValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCMoney")
     */
    private $moneyType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $FlyDate;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMAirway", inversedBy="cMAirTickets")
     */
    private $FlyAirway;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateSuggest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $des;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="no")
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea", inversedBy="cMAirTickets")
     */
    private $Area;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMAirTicketState")
     */
    private $ticketState;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buyDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $Buyer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $rejecter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMdaytime", inversedBy="cMAirTickets")
     */
    private $suggestTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMList")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmlist;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ARdate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ARdes;



    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination): void
    {
        $this->destination = $destination;
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
    public function getFlyDate()
    {
        return $this->FlyDate;
    }

    /**
     * @param mixed $FlyDate
     */
    public function setFlyDate($FlyDate): void
    {
        $this->FlyDate = $FlyDate;
    }

    /**
     * @return mixed
     */
    public function getFlyAirway()
    {
        return $this->FlyAirway;
    }

    /**
     * @param mixed $FlyAirway
     */
    public function setFlyAirway($FlyAirway): void
    {
        $this->FlyAirway = $FlyAirway;
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
    public function getDateSuggest()
    {
        return $this->dateSuggest;
    }

    /**
     * @param mixed $dateSuggest
     */
    public function setDateSuggest($dateSuggest): void
    {
        $this->dateSuggest = $dateSuggest;
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
    public function getArea()
    {
        return $this->Area;
    }

    /**
     * @param mixed $Area
     */
    public function setArea($Area): void
    {
        $this->Area = $Area;
    }

    /**
     * @return mixed
     */
    public function getTicketState()
    {
        return $this->ticketState;
    }

    /**
     * @param mixed $ticketState
     */
    public function setTicketState($ticketState): void
    {
        $this->ticketState = $ticketState;
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
    public function getBuyer()
    {
        return $this->Buyer;
    }

    /**
     * @param mixed $Buyer
     */
    public function setBuyer($Buyer): void
    {
        $this->Buyer = $Buyer;
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
    public function getFileID()
    {
        return $this->fileID;
    }

    /**
     * @param mixed $fileID
     */
    public function setFileID($fileID): void
    {
        $this->fileID = $fileID;
    }

    /**
     * @return mixed
     */
    public function getSuggestTime()
    {
        return $this->suggestTime;
    }

    /**
     * @param mixed $suggestTime
     */
    public function setSuggestTime($suggestTime): void
    {
        $this->suggestTime = $suggestTime;
    }

    public function getCmlist(): ?CMList
    {
        return $this->cmlist;
    }

    public function setCmlist(?CMList $cmlist): self
    {
        $this->cmlist = $cmlist;

        return $this;
    }

    public function getARdate(): ?string
    {
        return $this->ARdate;
    }

    public function setARdate(?string $ARdate): self
    {
        $this->ARdate = $ARdate;

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


}
