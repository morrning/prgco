<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ACCiccenterRepository")
 */
class ACCiccenter
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
    private $icname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iccode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCiccenter")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ACCdoc", mappedBy="iccenter")
     */
    private $aCCdocs;

    public function __construct()
    {
        $this->aCCdocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIcname(): ?string
    {
        return $this->icname;
    }

    public function setIcname(string $icname): self
    {
        $this->icname = $icname;

        return $this;
    }

    public function getIccode(): ?string
    {
        return $this->iccode;
    }

    public function setIccode(string $iccode): self
    {
        $this->iccode = $iccode;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|ACCdoc[]
     */
    public function getACCdocs(): Collection
    {
        return $this->aCCdocs;
    }

    public function addACCdoc(ACCdoc $aCCdoc): self
    {
        if (!$this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs[] = $aCCdoc;
            $aCCdoc->setIccenter($this);
        }

        return $this;
    }

    public function removeACCdoc(ACCdoc $aCCdoc): self
    {
        if ($this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs->removeElement($aCCdoc);
            // set the owning side to null (unless already changed)
            if ($aCCdoc->getIccenter() === $this) {
                $aCCdoc->setIccenter(null);
            }
        }

        return $this;
    }
}
