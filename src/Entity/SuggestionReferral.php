<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SuggestionReferralRepository")
 */
class SuggestionReferral
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Suggestion", inversedBy="suggestionReferrals")
     */
    private $suggestion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guestView;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $des;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dateSubmit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dateView;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SysPosition")
     */
    private $referralSource;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuggestion(): ?Suggestion
    {
        return $this->suggestion;
    }

    public function setSuggestion(?Suggestion $suggestion): self
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    public function getUser(): ?SysPosition
    {
        return $this->user;
    }

    public function setUser(?SysPosition $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGuestView(): ?string
    {
        return $this->guestView;
    }

    public function setGuestView(?string $guestView): self
    {
        $this->guestView = $guestView;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getDateView(): ?string
    {
        return $this->dateView;
    }

    public function setDateView(?string $dateView): self
    {
        $this->dateView = $dateView;

        return $this;
    }

    public function getReferralSource(): ?SysPosition
    {
        return $this->referralSource;
    }

    public function setReferralSource(?SysPosition $referralSource): self
    {
        $this->referralSource = $referralSource;

        return $this;
    }
}
