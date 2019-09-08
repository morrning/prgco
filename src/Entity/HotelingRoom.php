<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HotelingRoomRepository")
 */
class HotelingRoom
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\hotelingHotel", inversedBy="hotelingRooms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hotel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $num;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dep;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFull;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHotel(): ?hotelingHotel
    {
        return $this->hotel;
    }

    public function setHotel(?hotelingHotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getDep(): ?string
    {
        return $this->dep;
    }

    public function setDep(string $dep): self
    {
        $this->dep = $dep;

        return $this;
    }

    public function getIsFull(): ?bool
    {
        return $this->isFull;
    }

    public function setIsFull(?bool $isFull): self
    {
        $this->isFull = $isFull;

        return $this;
    }
}
