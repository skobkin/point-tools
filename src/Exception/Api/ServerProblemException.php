<?php
declare(strict_types=1);

namespace App\Exception\Api;

class ServerProblemException extends ApiException
{
    public function __construct(
        string $message = 'Server error',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}