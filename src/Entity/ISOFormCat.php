<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ISOFormCatRepository")
 */
class ISOFormCat
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
    private $catName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parrent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ISOForm", mappedBy="cat")
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

    public function getCatName(): ?string
    {
        return $this->catName;
    }

    public function setCatName(string $catName): self
    {
        $this->catName = $catName;

        return $this;
    }

    public function getParrent(): ?string
    {
        return $this->parrent;
    }

    public function setParrent(string $parrent): self
    {
        $this->parrent = $parrent;

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
            $iSOForm->setCat($this);
        }

        return $this;
    }

    public function removeISOForm(ISOForm $iSOForm): self
    {
        if ($this->iSOForms->contains($iSOForm)) {
            $this->iSOForms->removeElement($iSOForm);
            // set the owning side to null (unless already changed)
            if ($iSOForm->getCat() === $this) {
                $iSOForm->setCat(null);
            }
        }

        return $this;
    }
}
