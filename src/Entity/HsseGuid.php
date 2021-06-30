<?php

namespace App\Entity;

use App\Repository\HsseGuidRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HsseGuidRepository::class)
 */
class HsseGuid
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\ManyToOne(targetEntity=SysPosition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity=CMList::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmlist;

    /**
     * @ORM\ManyToOne(targetEntity=SysArea::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateDoing;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubmitter(): ?SysPosition
    {
        return $this->submitter;
    }

    public function setSubmitter(?SysPosition $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getCmlist(): ?CMList
    {
        return $this->cmlist;
    }

    public function setCmlist(?CMList $cmlist): self
    {
        $this->cmlist = $cmlist;

        return $this;
    }

    public function getArea(): ?SysArea
    {
        return $this->area;
    }

    public function setArea(?SysArea $area): self
    {
        $this->area = $area;

        return $this;
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

    public function getDateDoing(): ?string
    {
        return $this->dateDoing;
    }

    public function setDateDoing(?string $dateDoing): self
    {
        $this->dateDoing = $dateDoing;

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
