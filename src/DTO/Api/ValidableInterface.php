<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** @deprecated Use Symfony Validator instead */
interface ValidableInterface
{
    /** @deprecated Use Symfony Validator instead */
    public function isValid(): bool;
}
