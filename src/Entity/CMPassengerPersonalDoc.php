<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMPassengerPersonalDocRepository")
 */
class CMPassengerPersonalDoc
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
    private $docName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassenger", inversedBy="cMPassengerPersonalDocs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $passenger;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassengerDocType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $doctype;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocName(): ?string
    {
        return $this->docName;
    }

    public function setDocName(string $docName): self
    {
        $this->docName = $docName;

        return $this;
    }

    public function getPassenger(): ?CMPassenger
    {
        return $this->passenger;
    }

    public function setPassenger(?CMPassenger $passenger): self
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getDoctype(): ?CMPassengerDocType
    {
        return $this->doctype;
    }

    public function setDoctype(?CMPassengerDocType $doctype): self
    {
        $this->doctype = $doctype;

        return $this;
    }
}
