<?php

namespace App\Entity;

use App\Repository\AdmitadClientAuthRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdmitadClientAuthRepository::class)
 */
class AdmitadClientAuth
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $tokenType;

    /**
     * @ORM\Column(type="integer")
     */
    private $expiredTime;

//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    private $refreshToken;

    /**
     * @ORM\Column(type="array")
     */
    private $scopes = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function setTokenType(string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getExpiredTime(): ?int
    {
        return $this->expiredTime;
    }

    public function setExpiredTime(int $expiredTime): self
    {
        $this->expiredTime = $expiredTime;

        return $this;
    }

//    public function getRefreshToken(): ?string
//    {
//        return $this->refreshToken;
//    }
//
//    public function setRefreshToken(string $refreshToken): self
//    {
//        $this->refreshToken = $refreshToken;
//
//        return $this;
//    }

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }
}
