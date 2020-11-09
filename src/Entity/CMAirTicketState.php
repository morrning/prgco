<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMAirTicketStateRepository")
 */
class CMAirTicketState
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
     * @ORM\Column(type="string", length=255)
     */
    private $StateCode;

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

    public function getStateCode(): ?string
    {
        return $this->StateCode;
    }

    public function setStateCode(string $StateCode): self
    {
        $this->StateCode = $StateCode;

        return $this;
    }
}
