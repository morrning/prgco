<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysMenuItemRepository")
 */
class SysMenuItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysMenu", inversedBy="sysMenuItems")
     */
    private $menu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $internalUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fontawsome;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?SysMenu
    {
        return $this->menu;
    }

    public function setMenu(?SysMenu $menu): self
    {
        $this->menu = $menu;

        return $this;
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

    public function getInternalUrl(): ?string
    {
        return $this->internalUrl;
    }

    public function setInternalUrl(?string $internalUrl): self
    {
        $this->internalUrl = $internalUrl;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFontawsome(): ?string
    {
        return $this->fontawsome;
    }

    public function setFontawsome(?string $fontawsome): self
    {
        $this->fontawsome = $fontawsome;

        return $this;
    }
}
