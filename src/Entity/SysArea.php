<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysAreaRepository")
 */
class SysArea
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
    private $areaName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $des;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaName(): ?string
    {
        return $this->areaName;
    }

    public function setAreaName(string $areaName): self
    {
        $this->areaName = $areaName;

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
}
