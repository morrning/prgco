<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysPositionRepository")
 */
class SysPosition
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
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upperID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicLabel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NewsPost", mappedBy="submitter")
     */
    private $newsPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CMPassenger", mappedBy="submitter")
     */
    private $cMPassengers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser", inversedBy="sysPositions")
     */
    private $userID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysArea", inversedBy="sysPositions")
     */
    private $defaultArea;

    public function __construct()
    {
        $this->newsPosts = new ArrayCollection();
        $this->cMPassengers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getUpperID()
    {
        return $this->upperID;
    }

    /**
     * @param mixed $upperID
     */
    public function setUpperID($upperID): void
    {
        $this->upperID = $upperID;
    }

    /**
     * @return mixed
     */
    public function getPublicLabel()
    {
        return $this->publicLabel;
    }

    /**
     * @param mixed $publicLabel
     */
    public function setPublicLabel($publicLabel): void
    {
        $this->publicLabel = $publicLabel;
    }

    /**
     * @return mixed
     */
    public function getisDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @return Collection|NewsPost[]
     */
    public function getNewsPosts(): Collection
    {
        return $this->newsPosts;
    }

    public function addNewsPost(NewsPost $newsPost): self
    {
        if (!$this->newsPosts->contains($newsPost)) {
            $this->newsPosts[] = $newsPost;
            $newsPost->setSubmitter($this);
        }

        return $this;
    }

    public function removeNewsPost(NewsPost $newsPost): self
    {
        if ($this->newsPosts->contains($newsPost)) {
            $this->newsPosts->removeElement($newsPost);
            // set the owning side to null (unless already changed)
            if ($newsPost->getSubmitter() === $this) {
                $newsPost->setSubmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CMPassenger[]
     */
    public function getCMPassengers(): Collection
    {
        return $this->cMPassengers;
    }

    public function addCMPassenger(CMPassenger $cMPassenger): self
    {
        if (!$this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers[] = $cMPassenger;
            $cMPassenger->setSubmitter($this);
        }

        return $this;
    }

    public function removeCMPassenger(CMPassenger $cMPassenger): self
    {
        if ($this->cMPassengers->contains($cMPassenger)) {
            $this->cMPassengers->removeElement($cMPassenger);
            // set the owning side to null (unless already changed)
            if ($cMPassenger->getSubmitter() === $this) {
                $cMPassenger->setSubmitter(null);
            }
        }

        return $this;
    }

    public function getUserID(): ?SysUser
    {
        return $this->userID;
    }

    public function setUserID(?SysUser $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getDefaultArea(): ?SysArea
    {
        return $this->defaultArea;
    }

    public function setDefaultArea(?SysArea $defaultArea): self
    {
        $this->defaultArea = $defaultArea;

        return $this;
    }

}