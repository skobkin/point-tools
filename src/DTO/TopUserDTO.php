<?php
declare(strict_types=1);

namespace App\DTO;

/** Data Transfer Object for top users list */
class TopUserDTO
{
    public function __construct(
        public readonly string $login,
        public readonly int $subscribersCount,
    ) {
    }
}
