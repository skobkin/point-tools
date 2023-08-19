<?php
declare(strict_types=1);

namespace App\DTO\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Auth implements ValidableInterface
{
    public function __construct(
        #[SerializedName('token')]
        public readonly ?string $token,
        #[SerializedName('error')]
        public readonly ?string $error,
        #[SerializedName('csrf_token')]
        public readonly ?string $csRfToken,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->token && null !== $this->csRfToken && null === $this->error;
    }
}
