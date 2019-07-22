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
}
