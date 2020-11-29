<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HRMLetterOutCountryRepository")
 */
class HRMLetterOutCountry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser", inversedBy="hRMLetterOutCountries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $letterNum;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $letterStartDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $letterEndDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $letterSource;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?SysUser
    {
        return $this->user;
    }

    public function setUser(?SysUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLetterNum(): ?string
    {
        return $this->letterNum;
    }

    public function setLetterNum(string $letterNum): self
    {
        $this->letterNum = $letterNum;

        return $this;
    }

    public function getLetterStartDate(): ?string
    {
        return $this->letterStartDate;
    }

    public function setLetterStartDate(string $letterStartDate): self
    {
        $this->letterStartDate = $letterStartDate;

        return $this;
    }

    public function getLetterEndDate(): ?string
    {
        return $this->letterEndDate;
    }

    public function setLetterEndDate(string $letterEndDate): self
    {
        $this->letterEndDate = $letterEndDate;

        return $this;
    }

    public function getLetterSource(): ?string
    {
        return $this->letterSource;
    }

    public function setLetterSource(?string $letterSource): self
    {
        $this->letterSource = $letterSource;

        return $this;
    }
}
