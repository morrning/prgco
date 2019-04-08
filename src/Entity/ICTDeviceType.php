<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ICTDeviceTypeRepository")
 */
class ICTDeviceType
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
    private $TypeName;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->TypeName;
    }

    /**
     * @param mixed $TypeName
     */
    public function setTypeName($TypeName): void
    {
        $this->TypeName = $TypeName;
    }

}
