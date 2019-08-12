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
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassenger", inversedBy="cMAirTickets")
     */
    private $passengerID;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $FlyNumber;

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
    private $acceptDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buyDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $Buyer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rejectDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $rejecter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acceptDes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMacceptIF")
     */
    private $acceptIF;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMdaytime", inversedBy="cMAirTickets")
     */
    private $suggestTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rejectDes;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $flyTime;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassengerID(): ?CMPassenger
    {
        return $this->passengerID;
    }

    public function setPassengerID(?CMPassenger $passengerID): self
    {
        $this->passengerID = $passengerID;

        return $this;
    }

    public function getSource(): ?CMCities
    {
        return $this->source;
    }

    public function setSource(?CMCities $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getDestination(): ?CMCities
    {
        return $this->destination;
    }

    public function setDestination(?CMCities $destination): self
    {
        $this->destination = $destination;

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

    public function getMoneyValue(): ?string
    {
        return $this->moneyValue;
    }

    public function setMoneyValue(?string $moneyValue): self
    {
        $this->moneyValue = $moneyValue;

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

    public function getFlyDate(): ?string
    {
        return $this->FlyDate;
    }

    public function setFlyDate(?string $FlyDate): self
    {
        $this->FlyDate = $FlyDate;

        return $this;
    }

    public function getFlyNumber(): ?string
    {
        return $this->FlyNumber;
    }

    public function setFlyNumber(?string $FlyNumber): self
    {
        $this->FlyNumber = $FlyNumber;

        return $this;
    }

    public function getFlyAirway(): ?CMAirway
    {
        return $this->FlyAirway;
    }

    public function setFlyAirway(?CMAirway $FlyAirway): self
    {
        $this->FlyAirway = $FlyAirway;

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

    public function getDateSuggest(): ?string
    {
        return $this->dateSuggest;
    }

    public function setDateSuggest(?string $dateSuggest): self
    {
        $this->dateSuggest = $dateSuggest;

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

    public function getArea(): ?SysArea
    {
        return $this->Area;
    }

    public function setArea(?SysArea $Area): self
    {
        $this->Area = $Area;

        return $this;
    }

    public function getTicketState(): ?CMAirTicketState
    {
        return $this->ticketState;
    }

    public function setTicketState(?CMAirTicketState $ticketState): self
    {
        $this->ticketState = $ticketState;

        return $this;
    }

    public function getAcceptDate(): ?string
    {
        return $this->acceptDate;
    }

    public function setAcceptDate(?string $acceptDate): self
    {
        $this->acceptDate = $acceptDate;

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

    public function getBuyer(): ?SysPosition
    {
        return $this->Buyer;
    }

    public function setBuyer(?SysPosition $Buyer): self
    {
        $this->Buyer = $Buyer;

        return $this;
    }

    public function getRejectDate(): ?string
    {
        return $this->rejectDate;
    }

    public function setRejectDate(?string $rejectDate): self
    {
        $this->rejectDate = $rejectDate;

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

    public function getFileID(): ?string
    {
        return $this->fileID;
    }

    public function setFileID(?string $fileID): self
    {
        $this->fileID = $fileID;

        return $this;
    }

    public function getAcceptDes(): ?string
    {
        return $this->acceptDes;
    }

    public function setAcceptDes(?string $acceptDes): self
    {
        $this->acceptDes = $acceptDes;

        return $this;
    }

    public function getAcceptIF(): ?CMacceptIF
    {
        return $this->acceptIF;
    }

    public function setAcceptIF(?CMacceptIF $acceptIF): self
    {
        $this->acceptIF = $acceptIF;

        return $this;
    }

    public function getSuggestTime(): ?CMdaytime
    {
        return $this->suggestTime;
    }

    public function setSuggestTime(?CMdaytime $suggestTime): self
    {
        $this->suggestTime = $suggestTime;

        return $this;
    }

    public function getRejectDes(): ?string
    {
        return $this->rejectDes;
    }

    public function setRejectDes(?string $rejectDes): self
    {
        $this->rejectDes = $rejectDes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlyTime()
    {
        return $this->flyTime;
    }

    /**
     * @param mixed $flyTime
     */
    public function setFlyTime($flyTime): void
    {
        $this->flyTime = $flyTime;
    }


}
