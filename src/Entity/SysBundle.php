<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysBundleRepository")
 */
class SysBundle
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
    private $bundleName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDisabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $des;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBundleName(): ?string
    {
        return $this->bundleName;
    }

    public function setBundleName(string $bundleName): self
    {
        $this->bundleName = $bundleName;

        return $this;
    }

    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setIsDisabled(?bool $isDisabled): self
    {
        $this->isDisabled = $isDisabled;

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
}
