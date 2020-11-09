<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysHelpRepository")
 */
class SysHelp
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
    private $hid;

    /**
     * @ORM\Column(type="text")
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bundle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHid(): ?string
    {
        return $this->hid;
    }

    public function setHid(string $hid): self
    {
        $this->hid = $hid;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getBundle(): ?string
    {
        return $this->bundle;
    }

    public function setBundle(string $bundle): self
    {
        $this->bundle = $bundle;

        return $this;
    }
}
