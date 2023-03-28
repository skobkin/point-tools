<?php
declare(strict_types=1);

namespace App\DTO\Api;

interface ValidableInterface
{
    public function isValid(): bool;
}
