<?php

namespace App\Entity;

use App\Repository\HsseHurtRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HsseHurtRepository::class)
 */
class HsseHurt
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SysPosition::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $htime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $htype;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $doctorDoing;

    /**
     * @ORM\ManyToOne(targetEntity=CMList::class)
     */
    private $cmlist;

    /**
     * @ORM\ManyToOne(targetEntity=SysArea::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    /**
     * @ORM\Column(type="integer")
     */
    private $hgraid;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getHdate(): ?string
    {
        return $this->hdate;
    }

    public function setHdate(string $hdate): self
    {
        $this->hdate = $hdate;

        return $this;
    }

    public function getHtime(): ?string
    {
        return $this->htime;
    }

    public function setHtime(string $htime): self
    {
        $this->htime = $htime;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getHtype(): ?string
    {
        return $this->htype;
    }

    public function setHtype(string $htype): self
    {
        $this->htype = $htype;

        return $this;
    }

    public function getDoctorDoing(): ?string
    {
        return $this->doctorDoing;
    }

    public function setDoctorDoing(?string $doctorDoing): self
    {
        $this->doctorDoing = $doctorDoing;

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

    public function getHgraid(): ?int
    {
        return $this->hgraid;
    }

    public function setHgraid(int $hgraid): self
    {
        $this->hgraid = $hgraid;

        return $this;
    }
}
