<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HRMemployeRepository")
 */
class HRMemploye
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nationalCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shenasnameh;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $father;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adr;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $tel;

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

    public function getNationalCode(): ?string
    {
        return $this->nationalCode;
    }

    public function setNationalCode(string $nationalCode): self
    {
        $this->nationalCode = $nationalCode;

        return $this;
    }

    public function getShenasnameh(): ?string
    {
        return $this->shenasnameh;
    }

    public function setShenasnameh(string $shenasnameh): self
    {
        $this->shenasnameh = $shenasnameh;

        return $this;
    }

    public function getFather(): ?string
    {
        return $this->father;
    }

    public function setFather(?string $father): self
    {
        $this->father = $father;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(?string $birthday): self
    {
        $this->birthday = $birthday;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }
}
