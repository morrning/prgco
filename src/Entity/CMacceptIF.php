<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMacceptIFRepository")
 */
class CMacceptIF
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
    private $ifName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ifCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIfName(): ?string
    {
        return $this->ifName;
    }

    public function setIfName(string $ifName): self
    {
        $this->ifName = $ifName;

        return $this;
    }

    public function getIfCode(): ?string
    {
        return $this->ifCode;
    }

    public function setIfCode(string $ifCode): self
    {
        $this->ifCode = $ifCode;

        return $this;
    }
}
