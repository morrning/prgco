<?php

namespace App\Entity;

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
}
