<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Exceptions\Factory;


use Exception;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\User;

class InvalidUserDataException extends \Exception
{
    /**
     * @var User
     */
    private $user;

    public function __construct($message = "", User $user, $code = 0, Exception $previous = null)
    {
        $this->user = $user;

        parent::__construct($message, $code, $previous);
    }

    public function getUser(): User
    {
        return $this->user;
    }
}