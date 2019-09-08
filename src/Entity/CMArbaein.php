<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMArbaeinRepository")
 */
class CMArbaein
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
    private $inputer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $outputer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codemeli;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $inputDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $outputDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $FGUID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CGUID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea")
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $completer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputer(): ?SysPosition
    {
        return $this->inputer;
    }

    public function setInputer(?SysPosition $inputer): self
    {
        $this->inputer = $inputer;

        return $this;
    }

    public function getOutputer(): ?SysPosition
    {
        return $this->outputer;
    }

    public function setOutputer(?SysPosition $outputer): self
    {
        $this->outputer = $outputer;

        return $this;
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

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(string $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodemeli()
    {
        return $this->codemeli;
    }

    /**
     * @param mixed $codemeli
     */
    public function setCodemeli($codemeli): void
    {
        $this->codemeli = $codemeli;
    }



    public function getInputDate(): ?string
    {
        return $this->inputDate;
    }

    public function setInputDate(string $inputDate): self
    {
        $this->inputDate = $inputDate;

        return $this;
    }

    public function getOutputDate(): ?string
    {
        return $this->outputDate;
    }

    public function setOutputDate(?string $outputDate): self
    {
        $this->outputDate = $outputDate;

        return $this;
    }

    public function getFGUID(): ?string
    {
        return $this->FGUID;
    }

    public function setFGUID(string $FGUID): self
    {
        $this->FGUID = $FGUID;

        return $this;
    }

    public function getCGUID(): ?string
    {
        return $this->CGUID;
    }

    public function setCGUID(string $CGUID): self
    {
        $this->CGUID = $CGUID;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getIsMan(): ?bool
    {
        return $this->isMan;
    }

    public function setIsMan(?bool $isMan): self
    {
        $this->isMan = $isMan;

        return $this;
    }

    public function getCompleter(): ?SysPosition
    {
        return $this->completer;
    }

    public function setCompleter(?SysPosition $completer): self
    {
        $this->completer = $completer;

        return $this;
    }
}
