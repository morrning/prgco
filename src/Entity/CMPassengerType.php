<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMPassengerTypeRepository")
 */
class CMPassengerType
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
    private $typeName;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hseGuide;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMPassenger", mappedBy="ptype")
     */
    private $cMPassengers;

    public function __construct()
    {
        $this->cMPassengers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHseGuide()
    {
        return $this->hseGuide;
    }

    /**
     * @param mixed $hseGuide
     */
    public function setHseGuide($hseGuide): void
    {
        $this->hseGuide = $hseGuide;
    }

    /**
     * @return Collection|CMPassenger[]
     */
    public function getCMPassengers(): Collection
    {
        return $this->cMPassengers;
    }

    public function addCMPassenger(CMPassenger $cMPassenger): self
    {
        if (!$this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers[] = $cMPassenger;
            $cMPassenger->setPtype($this);
        }

        return $this;
    }

    public function removeCMPassenger(CMPassenger $cMPassenger): self
    {
        if ($this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers->removeElement($cMPassenger);
            // set the owning side to null (unless already changed)
            if ($cMPassenger->getPtype() === $this) {
                $cMPassenger->setPtype(null);
            }
        }

        return $this;
    }

}
