<?php
declare(strict_types=1);

namespace App\Exception\Api;

class ForbiddenException extends ApiException
{
    public function __construct(
        string $message = 'Forbidden',
        int $code = 403,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}