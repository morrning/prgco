<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SuuportTicketRepository")
 */
class SuuportTicket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mainTicket;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser", inversedBy="suuportTickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $UID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMainTicket(): ?bool
    {
        return $this->mainTicket;
    }

    public function setMainTicket(?bool $mainTicket): self
    {
        $this->mainTicket = $mainTicket;

        return $this;
    }

    public function getSubmitter(): ?SysUser
    {
        return $this->submitter;
    }

    public function setSubmitter(?SysUser $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

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

    public function getUID(): ?string
    {
        return $this->UID;
    }

    public function setUID(string $UID): self
    {
        $this->UID = $UID;

        return $this;
    }
}
