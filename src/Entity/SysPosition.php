<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysPositionRepository")
 */
class SysPosition
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
    private $label;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $upperID;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicLabel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $groups;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUpperID(): ?int
    {
        return $this->upperID;
    }

    public function setUpperID(?int $upperID): self
    {
        $this->upperID = $upperID;

        return $this;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function setUserID(?int $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getPublicLabel(): ?string
    {
        return $this->publicLabel;
    }

    public function setPublicLabel(string $publicLabel): self
    {
        $this->publicLabel = $publicLabel;

        return $this;
    }

    public function getIsDefault(): ?int
    {
        return $this->isDefault;
    }

    public function setIsDefault(?int $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getGroups(): ?string
    {
        return $this->groups;
    }

    public function setGroups(?string $groups): self
    {
        $this->groups = $groups;

        return $this;
    }
}
