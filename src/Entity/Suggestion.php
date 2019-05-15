<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SuggestionRepository")
 */
class Suggestion
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
    private $SID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parrentID;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Stype;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSID(): ?string
    {
        return $this->SID;
    }

    public function setSID(string $SID): self
    {
        $this->SID = $SID;

        return $this;
    }

    public function getParrentID(): ?string
    {
        return $this->parrentID;
    }

    public function setParrentID(string $parrentID): self
    {
        $this->parrentID = $parrentID;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getSubmitter(): ?string
    {
        return $this->submitter;
    }

    public function setSubmitter(?string $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getStype(): ?string
    {
        return $this->Stype;
    }

    public function setStype(string $type): self
    {
        $this->Stype = $type;

        return $this;
    }
}
