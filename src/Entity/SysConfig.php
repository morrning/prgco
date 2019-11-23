<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysConfigRepository")
 */
class SysConfig
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
    private $siteName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $footerText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activeationCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_SG_SERVERNAME;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_SG_DATABASE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_SG_USERNAME;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_SG_PASSWORD;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $USERS_MAX_COOKIE_TIME;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteName(): ?string
    {
        return $this->siteName;
    }

    public function setSiteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getFooterText(): ?string
    {
        return $this->footerText;
    }

    public function setFooterText(?string $footerText): self
    {
        $this->footerText = $footerText;

        return $this;
    }

    public function getActiveationCode(): ?string
    {
        return $this->activeationCode;
    }

    public function setActiveationCode(?string $activeationCode): self
    {
        $this->activeationCode = $activeationCode;

        return $this;
    }

    public function getHRMSGSERVERNAME(): ?string
    {
        return $this->HRM_SG_SERVERNAME;
    }

    public function setHRMSGSERVERNAME(?string $HRM_SG_SERVERNAME): self
    {
        $this->HRM_SG_SERVERNAME = $HRM_SG_SERVERNAME;

        return $this;
    }

    public function getHRMSGDATABASE(): ?string
    {
        return $this->HRM_SG_DATABASE;
    }

    public function setHRMSGDATABASE(?string $HRM_SG_DATABASE): self
    {
        $this->HRM_SG_DATABASE = $HRM_SG_DATABASE;

        return $this;
    }

    public function getHRMSGUSERNAME(): ?string
    {
        return $this->HRM_SG_USERNAME;
    }

    public function setHRMSGUSERNAME(?string $HRM_SG_USERNAME): self
    {
        $this->HRM_SG_USERNAME = $HRM_SG_USERNAME;

        return $this;
    }

    public function getHRMSGPASSWORD(): ?string
    {
        return $this->HRM_SG_PASSWORD;
    }

    public function setHRMSGPASSWORD(?string $HRM_SG_PASSWORD): self
    {
        $this->HRM_SG_PASSWORD = $HRM_SG_PASSWORD;

        return $this;
    }

    public function getUSERSMAXCOOKIETIME(): ?string
    {
        return $this->USERS_MAX_COOKIE_TIME;
    }

    public function setUSERSMAXCOOKIETIME(?string $USERS_MAX_COOKIE_TIME): self
    {
        $this->USERS_MAX_COOKIE_TIME = $USERS_MAX_COOKIE_TIME;

        return $this;
    }
}
