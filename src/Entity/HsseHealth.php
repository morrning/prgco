<?php

namespace App\Entity;

use App\Repository\HsseHealthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HsseHealthRepository::class)
 */
class HsseHealth
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CMPassenger::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $passenger;

    /**
     * @ORM\Column(type="bigint")
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $drugs;

    /**
     * @ORM\ManyToOne(targetEntity=SysPosition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cause;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $whyCome;

    /**
     * @ORM\ManyToOne(targetEntity=SysArea::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $dateOut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $AMP;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $services;

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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getDrugs(): ?string
    {
        return $this->drugs;
    }

    public function setDrugs(?string $drugs): self
    {
        $this->drugs = $drugs;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(?string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getWhyCome(): ?string
    {
        return $this->whyCome;
    }

    public function setWhyCome(string $whyCome): self
    {
        $this->whyCome = $whyCome;

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

    public function getDateOut(): ?string
    {
        return $this->dateOut;
    }

    public function setDateOut(?string $dateOut): self
    {
        $this->dateOut = $dateOut;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getAMP(): ?string
    {
        return $this->AMP;
    }

    public function setAMP(?string $AMP): self
    {
        $this->AMP = $AMP;

        return $this;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(?string $services): self
    {
        $this->services = $services;

        return $this;
    }
}
