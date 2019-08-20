<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ISOFormTypeRepository")
 */
class ISOFormType
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
    private $typeName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ISOForm", mappedBy="formType")
     */
    private $iSOForms;

    public function __construct()
    {
        $this->iSOForms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    public function getTypeCode(): ?string
    {
        return $this->typeCode;
    }

    public function setTypeCode(string $typeCode): self
    {
        $this->typeCode = $typeCode;

        return $this;
    }

    /**
     * @return Collection|ISOForm[]
     */
    public function getISOForms(): Collection
    {
        return $this->iSOForms;
    }

    public function addISOForm(ISOForm $iSOForm): self
    {
        if (!$this->iSOForms->contains($iSOForm)) {
            $this->iSOForms[] = $iSOForm;
            $iSOForm->setFormType($this);
        }

        return $this;
    }

    public function removeISOForm(ISOForm $iSOForm): self
    {
        if ($this->iSOForms->contains($iSOForm)) {
            $this->iSOForms->removeElement($iSOForm);
            // set the owning side to null (unless already changed)
            if ($iSOForm->getFormType() === $this) {
                $iSOForm->setFormType(null);
            }
        }

        return $this;
    }
}
