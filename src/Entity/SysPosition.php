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
     * @ORM\Column(type="string", length=255, nullable=true)
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $defaultArea;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getUpperID()
    {
        return $this->upperID;
    }

    /**
     * @param mixed $upperID
     */
    public function setUpperID($upperID): void
    {
        $this->upperID = $upperID;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID): void
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getPublicLabel()
    {
        return $this->publicLabel;
    }

    /**
     * @param mixed $publicLabel
     */
    public function setPublicLabel($publicLabel): void
    {
        $this->publicLabel = $publicLabel;
    }

    /**
     * @return mixed
     */
    public function getisDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @return mixed
     */
    public function getDefaultArea()
    {
        return $this->defaultArea;
    }

    /**
     * @param mixed $defaultArea
     */
    public function setDefaultArea($defaultArea): void
    {
        $this->defaultArea = $defaultArea;
    }


}