<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTMachineRepository")
 */
class ICTMachine
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
    private $machineName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $areaID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ownerID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCMainboard;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCRam;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCCpu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCHard;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ProdectCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PCBrand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMachineName(): ?string
    {
        return $this->machineName;
    }

    public function setMachineName(string $machineName): self
    {
        $this->machineName = $machineName;

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

    public function getAreaID(): ?string
    {
        return $this->areaID;
    }

    public function setAreaID(string $areaID): self
    {
        $this->areaID = $areaID;

        return $this;
    }

    public function getOwnerID(): ?string
    {
        return $this->ownerID;
    }

    public function setOwnerID(string $ownerID): self
    {
        $this->ownerID = $ownerID;

        return $this;
    }

    public function getPCMainboard(): ?string
    {
        return $this->PCMainboard;
    }

    public function setPCMainboard(?string $PCMainboard): self
    {
        $this->PCMainboard = $PCMainboard;

        return $this;
    }

    public function getPCRam(): ?string
    {
        return $this->PCRam;
    }

    public function setPCRam(?string $PCRam): self
    {
        $this->PCRam = $PCRam;

        return $this;
    }

    public function getPCCpu(): ?string
    {
        return $this->PCCpu;
    }

    public function setPCCpu(?string $PCCpu): self
    {
        $this->PCCpu = $PCCpu;

        return $this;
    }

    public function getPCHard(): ?string
    {
        return $this->PCHard;
    }

    public function setPCHard(?string $PCHard): self
    {
        $this->PCHard = $PCHard;

        return $this;
    }

    public function getProdectCode(): ?string
    {
        return $this->ProdectCode;
    }

    public function setProdectCode(?string $ProdectCode): self
    {
        $this->ProdectCode = $ProdectCode;

        return $this;
    }

    public function getPCName(): ?string
    {
        return $this->PCName;
    }

    public function setPCName(?string $PCName): self
    {
        $this->PCName = $PCName;

        return $this;
    }

    public function getPCBrand(): ?string
    {
        return $this->PCBrand;
    }

    public function setPCBrand(?string $PCBrand): self
    {
        $this->PCBrand = $PCBrand;

        return $this;
    }
}
