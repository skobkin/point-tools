<?php

namespace src\PointToolsBundle\DTO\Api;

use src\PointToolsBundle\DTO\Api\ValidableInterface;

class Auth implements ValidableInterface
{
    /**
     * @var string|null
     */
    private $token;

    /**
     * @var string|null
     */
    private $csRfToken;

    /**
     * @var string|null
     */
    private $error;


    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getCsRfToken(): ?string
    {
        return $this->csRfToken;
    }

    public function setCsRfToken(?string $csRfToken): void
    {
        $this->csRfToken = $csRfToken;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function isValid(): bool
    {
        if (null !== $this->token && null !== $this->csRfToken && null === $this->error) {
            return true;
        }

        return false;
    }
}