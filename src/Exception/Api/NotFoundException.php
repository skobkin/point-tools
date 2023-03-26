<?php
declare(strict_types=1);

namespace App\Exception\Api;

class NotFoundException extends ApiException
{
    public function __construct(
        string $message = 'Resource not found',
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
