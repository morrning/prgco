<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTRequestTypeRepository")
 */
class ICTRequestType
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
     * @ORM\OneToMany(targetEntity="App\Entity\ICTRequest", mappedBy="requestType")
     */
    private $iCTRequests;

    public function __construct()
    {
        $this->iCTRequests = new ArrayCollection();
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
            $iCTRequest->setRequestType($this);
        }

        return $this;
    }

    public function removeICTRequest(ICTRequest $iCTRequest): self
    {
        if ($this->iCTRequests->contains($iCTRequest)) {
            $this->iCTRequests->removeElement($iCTRequest);
            // set the owning side to null (unless already changed)
            if ($iCTRequest->getRequestType() === $this) {
                $iCTRequest->setRequestType(null);
            }
        }

        return $this;
    }
}
