<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ACCdocItemRepository")
 */
class ACCdocItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCdoc", inversedBy="aCCdocItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $doc;

    /**
     * @ORM\Column(type="integer")
     */
    private $moneyValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ACCMoney")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moneyType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoc(): ?ACCdoc
    {
        return $this->doc;
    }

    public function setDoc(?ACCdoc $doc): self
    {
        $this->doc = $doc;

        return $this;
    }

    public function getMoneyValue(): ?int
    {
        return $this->moneyValue;
    }

    public function setMoneyValue(int $moneyValue): self
    {
        $this->moneyValue = $moneyValue;

        return $this;
    }

    public function getMoneyType(): ?ACCMoney
    {
        return $this->moneyType;
    }

    public function setMoneyType(?ACCMoney $moneyType): self
    {
        $this->moneyType = $moneyType;

        return $this;
    }
}
