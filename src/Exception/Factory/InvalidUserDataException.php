<?php
declare(strict_types=1);

namespace App\Exception\Factory;

use App\DTO\Api\User as UserDTO;

class InvalidUserDataException extends \Exception
{
    public function __construct(
        public readonly UserDTO $user,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct('Invalid user data', $code, $previous);
    }
}
