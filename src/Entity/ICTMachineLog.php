<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTMachineLogRepository")
 */
class ICTMachineLog
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
    private $machineID;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $logType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMachineID(): ?string
    {
        return $this->machineID;
    }

    public function setMachineID(string $machineID): self
    {
        $this->machineID = $machineID;

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

    public function getLogType(): ?string
    {
        return $this->logType;
    }

    public function setLogType(string $logType): self
    {
        $this->logType = $logType;

        return $this;
    }
}
