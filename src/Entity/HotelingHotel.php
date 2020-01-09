<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HotelingHotelRepository")
 */
class HotelingHotel
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
    private $hotelName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adr;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea", inversedBy="hotelingHotels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HotelingRoom", mappedBy="hotel", orphanRemoval=true)
     */
    private $hotelingRooms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dep;

    public function __construct()
    {
        $this->hotelingRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHotelName(): ?string
    {
        return $this->hotelName;
    }

    public function setHotelName(string $hotelName): self
    {
        $this->hotelName = $hotelName;

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

    public function getArea(): ?SysArea
    {
        return $this->area;
    }

    public function setArea(?SysArea $area): self
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection|HotelingRoom[]
     */
    public function getHotelingRooms(): Collection
    {
        return $this->hotelingRooms;
    }

    public function addHotelingRoom(HotelingRoom $hotelingRoom): self
    {
        if (!$this->hotelingRooms->contains($hotelingRoom)) {
            $this->hotelingRooms[] = $hotelingRoom;
            $hotelingRoom->setHotel($this);
        }

        return $this;
    }

    public function removeHotelingRoom(HotelingRoom $hotelingRoom): self
    {
        if ($this->hotelingRooms->contains($hotelingRoom)) {
            $this->hotelingRooms->removeElement($hotelingRoom);
            // set the owning side to null (unless already changed)
            if ($hotelingRoom->getHotel() === $this) {
                $hotelingRoom->setHotel(null);
            }
        }

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getDep(): ?int
    {
        return $this->dep;
    }

    public function setDep(?int $dep): self
    {
        $this->dep = $dep;

        return $this;
    }
}
