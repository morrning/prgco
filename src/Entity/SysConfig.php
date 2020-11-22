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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $SYS_LOGIN_BGRND;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $SYS_FONT_NAME;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_PW_SERVERNAME;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_PW_DATABASE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_PW_USERNAME;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $HRM_PW_PASSWORD;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $SMS_API;

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

    public function getSYSLOGINBGRND(): ?string
    {
        return $this->SYS_LOGIN_BGRND;
    }

    public function setSYSLOGINBGRND(?string $SYS_LOGIN_BGRND): self
    {
        $this->SYS_LOGIN_BGRND = $SYS_LOGIN_BGRND;

        return $this;
    }

    public function getSYSFONTNAME(): ?string
    {
        return $this->SYS_FONT_NAME;
    }

    public function setSYSFONTNAME(?string $SYS_FONT_NAME): self
    {
        $this->SYS_FONT_NAME = $SYS_FONT_NAME;

        return $this;
    }

    public function getHRMPWSERVERNAME(): ?string
    {
        return $this->HRM_PW_SERVERNAME;
    }

    public function setHRMPWSERVERNAME(?string $HRM_PW_SERVERNAME): self
    {
        $this->HRM_PW_SERVERNAME = $HRM_PW_SERVERNAME;

        return $this;
    }

    public function getHRMPWDATABASE(): ?string
    {
        return $this->HRM_PW_DATABASE;
    }

    public function setHRMPWDATABASE(?string $HRM_PW_DATABASE): self
    {
        $this->HRM_PW_DATABASE = $HRM_PW_DATABASE;

        return $this;
    }

    public function getHRMPWUSERNAME(): ?string
    {
        return $this->HRM_PW_USERNAME;
    }

    public function setHRMPWUSERNAME(?string $HRM_PW_USERNAME): self
    {
        $this->HRM_PW_USERNAME = $HRM_PW_USERNAME;

        return $this;
    }

    public function getHRMPWPASSWORD(): ?string
    {
        return $this->HRM_PW_PASSWORD;
    }

    public function setHRMPWPASSWORD(?string $HRM_PW_PASSWORD): self
    {
        $this->HRM_PW_PASSWORD = $HRM_PW_PASSWORD;

        return $this;
    }

    public function getSMSAPI(): ?string
    {
        return $this->SMS_API;
    }

    public function setSMSAPI(?string $SMS_API): self
    {
        $this->SMS_API = $SMS_API;

        return $this;
    }
}
