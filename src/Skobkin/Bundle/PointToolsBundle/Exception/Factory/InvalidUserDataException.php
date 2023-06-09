<?php

namespace Skobkin\Bundle\PointToolsBundle\Exception\Factory;

use Skobkin\Bundle\PointToolsBundle\DTO\Api\User as UserDTO;

class InvalidUserDataException extends \Exception
{
    /**
     * @var UserDTO
     */
    private $user;

    public function __construct($message = "", UserDTO $user, $code = 0, \Exception $previous = null)
    {
        $this->user = $user;

        parent::__construct($message, $code, $previous);
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }
}