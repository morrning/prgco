<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysNotificationRepository")
 */
class SysNotification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="sysNotifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userID;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $viewed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkTarget;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserID(): ?SysPosition
    {
        return $this->userID;
    }

    public function setUserID(?SysPosition $userID): self
    {
        $this->userID = $userID;

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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getViewed(): ?string
    {
        return $this->viewed;
    }

    public function setViewed(?string $viewed): self
    {
        $this->viewed = $viewed;

        return $this;
    }

    public function getLinkTarget(): ?string
    {
        return $this->linkTarget;
    }

    public function setLinkTarget(?string $linkTarget): self
    {
        $this->linkTarget = $linkTarget;

        return $this;
    }
}
