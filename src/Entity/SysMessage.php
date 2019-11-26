<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysMessageRepository")
 */
class SysMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateSend;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reciver;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateView;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mtitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mdes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachedFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?SysPosition
    {
        return $this->sender;
    }

    public function setSender(?SysPosition $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getDateSend(): ?string
    {
        return $this->dateSend;
    }

    public function setDateSend(?string $dateSend): self
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    public function getReciver(): ?SysPosition
    {
        return $this->reciver;
    }

    public function setReciver(?SysPosition $reciver): self
    {
        $this->reciver = $reciver;

        return $this;
    }

    public function getDateView(): ?string
    {
        return $this->dateView;
    }

    public function setDateView(?string $dateView): self
    {
        $this->dateView = $dateView;

        return $this;
    }

    public function getMtitle(): ?string
    {
        return $this->mtitle;
    }

    public function setMtitle(string $mtitle): self
    {
        $this->mtitle = $mtitle;

        return $this;
    }

    public function getMdes(): ?string
    {
        return $this->mdes;
    }

    public function setMdes(?string $mdes): self
    {
        $this->mdes = $mdes;

        return $this;
    }

    public function getAttachedFile(): ?string
    {
        return $this->attachedFile;
    }

    public function setAttachedFile(?string $attachedFile): self
    {
        $this->attachedFile = $attachedFile;

        return $this;
    }
}
