<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysHelpContentRepository")
 */
class SysHelpContent
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysHelpContent", inversedBy="sysHelpContents")
     */
    private $parrent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SysHelpContent", mappedBy="parrent")
     */
    private $sysHelpContents;

    public function __construct()
    {
        $this->parrent = new ArrayCollection();
        $this->sysHelpContents = new ArrayCollection();
    }

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getParrent(): ?self
    {
        return $this->parrent;
    }

    public function setParrent(?self $parrent): self
    {
        $this->parrent = $parrent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSysHelpContents(): Collection
    {
        return $this->sysHelpContents;
    }

    public function addSysHelpContent(self $sysHelpContent): self
    {
        if (!$this->sysHelpContents->contains($sysHelpContent)) {
            $this->sysHelpContents[] = $sysHelpContent;
            $sysHelpContent->setParrent($this);
        }

        return $this;
    }

    public function removeSysHelpContent(self $sysHelpContent): self
    {
        if ($this->sysHelpContents->contains($sysHelpContent)) {
            $this->sysHelpContents->removeElement($sysHelpContent);
            // set the owning side to null (unless already changed)
            if ($sysHelpContent->getParrent() === $this) {
                $sysHelpContent->setParrent(null);
            }
        }

        return $this;
    }
}
