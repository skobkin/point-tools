<?php
declare(strict_types=1);

namespace App\Exception\Api;

class UserNotFoundException extends NotFoundException
{
    public function __construct(
        $message = 'User not found',
        $code = 0,
        \Exception $previous = null,
        private readonly ?int $userId = null,
        private readonly ?string $login = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }
}
