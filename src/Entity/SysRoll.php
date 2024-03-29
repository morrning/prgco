<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SysRollRepository")
 */
class SysRoll
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
    private $label;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ictRequest;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ictDoing;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $newsPublish;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $MostUsedFiles;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $phonebookAdmin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $suggestionInbox;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ISOfORMS;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $CountDown;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $projectAdmin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $CeremonailOPTDashboard;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ceremonialHotelingOPT;

    /**
     * @return mixed
     */
    public function getCeremonialHotelingOPT()
    {
        return $this->ceremonialHotelingOPT;
    }

    /**
     * @param mixed $ceremonialHotelingOPT
     */
    public function setCeremonialHotelingOPT($ceremonialHotelingOPT): void
    {
        $this->ceremonialHotelingOPT = $ceremonialHotelingOPT;
    }

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $CeremonailREQ;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $CeremonailMNGDashboard;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $superAdmin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pmAccess;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $HRMACCESS;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $HRMACCESSTOTAL;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $HSSEAREA;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getIctRequest(): ?bool
    {
        return $this->ictRequest;
    }

    public function setIctRequest(?bool $ictRequest): self
    {
        $this->ictRequest = $ictRequest;

        return $this;
    }

    public function getIctDoing(): ?bool
    {
        return $this->ictDoing;
    }

    public function setIctDoing(?bool $ictDoing): self
    {
        $this->ictDoing = $ictDoing;

        return $this;
    }

    public function getNewsPublish(): ?bool
    {
        return $this->newsPublish;
    }

    public function setNewsPublish(?bool $newsPublish): self
    {
        $this->newsPublish = $newsPublish;

        return $this;
    }

    public function getMostUsedFiles(): ?bool
    {
        return $this->MostUsedFiles;
    }

    public function setMostUsedFiles(?bool $MostUsedFiles): self
    {
        $this->MostUsedFiles = $MostUsedFiles;

        return $this;
    }

    public function getPhonebookAdmin(): ?bool
    {
        return $this->phonebookAdmin;
    }

    public function setPhonebookAdmin(?bool $phonebookAdmin): self
    {
        $this->phonebookAdmin = $phonebookAdmin;

        return $this;
    }

    public function getSuggestionInbox(): ?bool
    {
        return $this->suggestionInbox;
    }

    public function setSuggestionInbox(?bool $suggestionInbox): self
    {
        $this->suggestionInbox = $suggestionInbox;

        return $this;
    }

    public function getISOfORMS(): ?bool
    {
        return $this->ISOfORMS;
    }

    public function setISOfORMS(?bool $ISOfORMS): self
    {
        $this->ISOfORMS = $ISOfORMS;

        return $this;
    }

    public function getCountDown(): ?bool
    {
        return $this->CountDown;
    }

    public function setCountDown(?bool $CountDown): self
    {
        $this->CountDown = $CountDown;

        return $this;
    }

    public function getProjectAdmin(): ?bool
    {
        return $this->projectAdmin;
    }

    public function setProjectAdmin(?bool $projectAdmin): self
    {
        $this->projectAdmin = $projectAdmin;

        return $this;
    }

    public function getCeremonailOPTDashboard(): ?bool
    {
        return $this->CeremonailOPTDashboard;
    }

    public function setCeremonailOPTDashboard(?bool $CeremonailOPTDashboard): self
    {
        $this->CeremonailOPTDashboard = $CeremonailOPTDashboard;

        return $this;
    }

    public function getCeremonailREQ(): ?bool
    {
        return $this->CeremonailREQ;
    }

    public function setCeremonailREQ(?bool $CeremonailREQ): self
    {
        $this->CeremonailREQ = $CeremonailREQ;

        return $this;
    }

    public function getCeremonailMNGDashboard(): ?bool
    {
        return $this->CeremonailMNGDashboard;
    }

    public function setCeremonailMNGDashboard(?bool $CeremonailMNGDashboard): self
    {
        $this->CeremonailMNGDashboard = $CeremonailMNGDashboard;

        return $this;
    }

    public function getSuperAdmin(): ?bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(?bool $superAdmin): self
    {
        $this->superAdmin = $superAdmin;

        return $this;
    }

    public function getPmAccess(): ?bool
    {
        return $this->pmAccess;
    }

    public function setPmAccess(?bool $pmAccess): self
    {
        $this->pmAccess = $pmAccess;

        return $this;
    }

    public function getHRMACCESS(): ?bool
    {
        return $this->HRMACCESS;
    }

    public function setHRMACCESS(?bool $HRMACCESS): self
    {
        $this->HRMACCESS = $HRMACCESS;

        return $this;
    }

    public function getHRMACCESSTOTAL(): ?bool
    {
        return $this->HRMACCESSTOTAL;
    }

    public function setHRMACCESSTOTAL(?bool $HRMACCESSTOTAL): self
    {
        $this->HRMACCESSTOTAL = $HRMACCESSTOTAL;

        return $this;
    }

    public function getHSSEAREA(): ?int
    {
        return $this->HSSEAREA;
    }

    public function setHSSEAREA(?int $HSSEAREA): self
    {
        $this->HSSEAREA = $HSSEAREA;

        return $this;
    }
}
