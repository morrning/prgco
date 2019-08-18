<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ACCMoneyRepository")
 */
class ACCMoney
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
    private $moneyName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moneyCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ACCdoc", mappedBy="Money")
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

    public function getMoneyName(): ?string
    {
        return $this->moneyName;
    }

    public function setMoneyName(string $moneyName): self
    {
        $this->moneyName = $moneyName;

        return $this;
    }

    public function getMoneyCode(): ?string
    {
        return $this->moneyCode;
    }

    public function setMoneyCode(?string $moneyCode): self
    {
        $this->moneyCode = $moneyCode;

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
            $aCCdoc->setMoney($this);
        }

        return $this;
    }

    public function removeACCdoc(ACCdoc $aCCdoc): self
    {
        if ($this->aCCdocs->contains($aCCdoc)) {
            $this->aCCdocs->removeElement($aCCdoc);
            // set the owning side to null (unless already changed)
            if ($aCCdoc->getMoney() === $this) {
                $aCCdoc->setMoney(null);
            }
        }

        return $this;
    }
}
