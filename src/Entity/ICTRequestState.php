<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTRequestStateRepository")
 */
class ICTRequestState
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
    private $stateName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ICTRequest", mappedBy="state")
     */
    private $iCTRequests;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stateCode;

    public function __construct()
    {
        $this->iCTRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    public function setStateName(string $stateName): self
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * @return Collection|ICTRequest[]
     */
    public function getICTRequests(): Collection
    {
        return $this->iCTRequests;
    }

    public function addICTRequest(ICTRequest $iCTRequest): self
    {
        if (!$this->iCTRequests->contains($iCTRequest)) {
            $this->iCTRequests[] = $iCTRequest;
            $iCTRequest->setState($this);
        }

        return $this;
    }

    public function removeICTRequest(ICTRequest $iCTRequest): self
    {
        if ($this->iCTRequests->contains($iCTRequest)) {
            $this->iCTRequests->removeElement($iCTRequest);
            // set the owning side to null (unless already changed)
            if ($iCTRequest->getState() === $this) {
                $iCTRequest->setState(null);
            }
        }

        return $this;
    }

    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    public function setStateCode(?string $stateCode): self
    {
        $this->stateCode = $stateCode;

        return $this;
    }
}
