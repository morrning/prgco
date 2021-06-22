<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MostUsedFileCatRepository")
 */
class MostUsedFileCat
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
     * @ORM\OneToMany(targetEntity="App\Entity\MostUsedFile", mappedBy="cat")
     */
    private $mostUsedFiles;

    public function __construct()
    {
        $this->mostUsedFiles = new ArrayCollection();
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

    /**
     * @return Collection|MostUsedFile[]
     */
    public function getMostUsedFiles(): Collection
    {
        return $this->mostUsedFiles;
    }

    public function addMostUsedFile(MostUsedFile $mostUsedFile): self
    {
        if (!$this->mostUsedFiles->contains($mostUsedFile)) {
            $this->mostUsedFiles[] = $mostUsedFile;
            $mostUsedFile->setCat($this);
        }

        return $this;
    }

    public function removeMostUsedFile(MostUsedFile $mostUsedFile): self
    {
        if ($this->mostUsedFiles->contains($mostUsedFile)) {
            $this->mostUsedFiles->removeElement($mostUsedFile);
            // set the owning side to null (unless already changed)
            if ($mostUsedFile->getCat() === $this) {
                $mostUsedFile->setCat(null);
            }
        }

        return $this;
    }
}
