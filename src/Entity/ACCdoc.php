<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ACCdocRepository")
 */
class ACCdoc
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $docTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ACCdocItem", mappedBy="doc", orphanRemoval=true)
     */
    private $aCCdocItems;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCiccenter", inversedBy="aCCdocs")
     */
    private $iccenter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition", inversedBy="aCCdocs")
     */
    private $icuser;

    public function __construct()
    {
        $this->aCCdocItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocTitle(): ?string
    {
        return $this->docTitle;
    }

    public function setDocTitle(?string $docTitle): self
    {
        $this->docTitle = $docTitle;

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

    public function getSubmitter(): ?SysPosition
    {
        return $this->submitter;
    }

    public function setSubmitter(?SysPosition $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    /**
     * @return Collection|ACCdocItem[]
     */
    public function getACCdocItems(): Collection
    {
        return $this->aCCdocItems;
    }

    public function addACCdocItem(ACCdocItem $aCCdocItem): self
    {
        if (!$this->aCCdocItems->contains($aCCdocItem)) {
            $this->aCCdocItems[] = $aCCdocItem;
            $aCCdocItem->setDoc($this);
        }

        return $this;
    }

    public function removeACCdocItem(ACCdocItem $aCCdocItem): self
    {
        if ($this->aCCdocItems->contains($aCCdocItem)) {
            $this->aCCdocItems->removeElement($aCCdocItem);
            // set the owning side to null (unless already changed)
            if ($aCCdocItem->getDoc() === $this) {
                $aCCdocItem->setDoc(null);
            }
        }

        return $this;
    }

    public function getIccenter(): ?ACCiccenter
    {
        return $this->iccenter;
    }

    public function setIccenter(?ACCiccenter $iccenter): self
    {
        $this->iccenter = $iccenter;

        return $this;
    }

    public function getIcuser(): ?SysPosition
    {
        return $this->icuser;
    }

    public function setIcuser(?SysPosition $icuser): self
    {
        $this->icuser = $icuser;

        return $this;
    }
}