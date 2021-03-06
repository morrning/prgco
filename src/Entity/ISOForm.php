<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ISOFormRepository")
 */
class ISOForm
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ISOCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileExt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ISOFormCat", inversedBy="iSOForms")
     */
    private $cat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ISOFormType", inversedBy="iSOForms")
     */
    private $formType;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getFileID()
    {
        return $this->fileID;
    }

    /**
     * @param mixed $fileID
     */
    public function setFileID($fileID): void
    {
        $this->fileID = $fileID;
    }

    /**
     * @return mixed
     */
    public function getSubmitter()
    {
        return $this->submitter;
    }

    /**
     * @param mixed $submitter
     */
    public function setSubmitter($submitter): void
    {
        $this->submitter = $submitter;
    }

    /**
     * @return mixed
     */
    public function getISOCode()
    {
        return $this->ISOCode;
    }

    /**
     * @param mixed $ISOCode
     */
    public function setISOCode($ISOCode): void
    {
        $this->ISOCode = $ISOCode;
    }

    /**
     * @return mixed
     */
    public function getDateSubmit()
    {
        return $this->dateSubmit;
    }

    /**
     * @param mixed $dateSubmit
     */
    public function setDateSubmit($dateSubmit): void
    {
        $this->dateSubmit = $dateSubmit;
    }

    /**
     * @return mixed
     */
    public function getFileExt()
    {
        return $this->fileExt;
    }

    /**
     * @param mixed $fileExt
     */
    public function setFileExt($fileExt): void
    {
        $this->fileExt = $fileExt;
    }

    public function getCat(): ?ISOFormCat
    {
        return $this->cat;
    }

    public function setCat(?ISOFormCat $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

    public function getFormType(): ?ISOFormType
    {
        return $this->formType;
    }

    public function setFormType(?ISOFormType $formType): self
    {
        $this->formType = $formType;

        return $this;
    }
    

}
