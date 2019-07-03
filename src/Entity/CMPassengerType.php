<?php

namespace App\Entity;

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

}
