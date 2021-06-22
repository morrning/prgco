<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMListUserRepository")
 */
class CMListUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMList")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmlist;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CMPassenger")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmpassenger;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmlist(): ?CMList
    {
        return $this->cmlist;
    }

    public function setCmlist(?CMList $cmlist): self
    {
        $this->cmlist = $cmlist;

        return $this;
    }

    public function getCmpassenger(): ?CMPassenger
    {
        return $this->cmpassenger;
    }

    public function setCmpassenger(?CMPassenger $cmpassenger): self
    {
        $this->cmpassenger = $cmpassenger;

        return $this;
    }
}
