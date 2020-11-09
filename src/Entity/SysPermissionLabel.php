<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysPermissionLabelRepository")
 */
class SysPermissionLabel
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
    private $pname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plabel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPname(): ?string
    {
        return $this->pname;
    }

    public function setPname(string $pname): self
    {
        $this->pname = $pname;

        return $this;
    }

    public function getPlabel(): ?string
    {
        return $this->plabel;
    }

    public function setPlabel(string $plabel): self
    {
        $this->plabel = $plabel;

        return $this;
    }
}
