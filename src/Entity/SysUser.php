<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysUserRepository")
 */
class SysUser
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
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uniqueID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobileNum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUniqueID(): ?string
    {
        return $this->uniqueID;
    }

    public function setUniqueID(string $uniqueID): self
    {
        $this->uniqueID = $uniqueID;

        return $this;
    }

    public function getMobileNum(): ?string
    {
        return $this->mobileNum;
    }

    public function setMobileNum(?string $mobileNum): self
    {
        $this->mobileNum = $mobileNum;

        return $this;
    }
}
