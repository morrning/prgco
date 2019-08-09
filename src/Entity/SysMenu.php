<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysMenuRepository")
 */
class SysMenu
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
    private $menuName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $menuLabel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SysMenuItem", mappedBy="menu")
     */
    private $sysMenuItems;

    public function __construct()
    {
        $this->sysMenuItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuName(): ?string
    {
        return $this->menuName;
    }

    public function setMenuName(string $menuName): self
    {
        $this->menuName = $menuName;

        return $this;
    }

    public function getMenuLabel(): ?string
    {
        return $this->menuLabel;
    }

    public function setMenuLabel(string $menuLabel): self
    {
        $this->menuLabel = $menuLabel;

        return $this;
    }

    /**
     * @return Collection|SysMenuItem[]
     */
    public function getSysMenuItems(): Collection
    {
        return $this->sysMenuItems;
    }

    public function addSysMenuItem(SysMenuItem $sysMenuItem): self
    {
        if (!$this->sysMenuItems->contains($sysMenuItem)) {
            $this->sysMenuItems[] = $sysMenuItem;
            $sysMenuItem->setMenu($this);
        }

        return $this;
    }

    public function removeSysMenuItem(SysMenuItem $sysMenuItem): self
    {
        if ($this->sysMenuItems->contains($sysMenuItem)) {
            $this->sysMenuItems->removeElement($sysMenuItem);
            // set the owning side to null (unless already changed)
            if ($sysMenuItem->getMenu() === $this) {
                $sysMenuItem->setMenu(null);
            }
        }

        return $this;
    }
}
