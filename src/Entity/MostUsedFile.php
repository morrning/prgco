<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MostUsedFileRepository")
 */
class MostUsedFile
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $areaID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFileID(): ?string
    {
        return $this->fileID;
    }

    public function setFileID(string $fileID): self
    {
        $this->fileID = $fileID;

        return $this;
    }

    public function getSubmitter(): ?string
    {
        return $this->submitter;
    }

    public function setSubmitter(string $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getAreaID(): ?string
    {
        return $this->areaID;
    }

    public function setAreaID(string $areaID): self
    {
        $this->areaID = $areaID;

        return $this;
    }
}