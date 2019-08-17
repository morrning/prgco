<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMPassengerDocTypeRepository")
 */
class CMPassengerDocType
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
    private $tname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTname(): ?string
    {
        return $this->tname;
    }

    public function setTname(string $tname): self
    {
        $this->tname = $tname;

        return $this;
    }
}
