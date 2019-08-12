<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ACCaccountRepository")
 */
class ACCaccount
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SysUser")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sysposition")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ACCdoc", mappedBy="account")
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUser(): ?SysUser
    {
        return $this->user;
    }

    public function setUser(?SysUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPosition(): ?Sysposition
    {
        return $this->position;
    }

    public function setPosition(?Sysposition $position): self
    {
        $this->position = $position;

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
            $aCCdoc->setAccount($this);
        }

        return $this;
    }

    public function removeACCdoc(ACCdoc $aCCdoc): self
    {
        if ($this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs->removeElement($aCCdoc);
            // set the owning side to null (unless already changed)
            if ($aCCdoc->getAccount() === $this) {
                $aCCdoc->setAccount(null);
            }
        }

        return $this;
    }
}
