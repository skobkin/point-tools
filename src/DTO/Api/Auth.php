<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** TODO: Refactor to public readonly */
class Auth implements ValidableInterface
{
    private ?string $token;
    private ?string $csRfToken;
    private ?string $error;

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
        return null !== $this->token && null !== $this->csRfToken && null === $this->error;
    }
}
