<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CMCitiesRepository")
 */
class CMCities
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
    private $cname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCname(): ?string
    {
        return $this->cname;
    }

    public function setCname(string $cname): self
    {
        $this->cname = $cname;

        return $this;
    }
}
