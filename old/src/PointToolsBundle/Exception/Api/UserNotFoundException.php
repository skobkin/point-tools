<?php

namespace src\PointToolsBundle\Exception\Api;

use src\PointToolsBundle\Exception\Api\NotFoundException;

class UserNotFoundException extends NotFoundException
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $login;


    /**
     * {@inheritdoc}
     * @param int $userId
     */
    public function __construct($message = 'User not found', $code = 0, \Exception $previous = null, $userId = null, $login = null)
    {
        parent::__construct($message, $code, $previous);

        $this->userId = $userId;
        $this->login = $login;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getLogin(): string
    {
        return $this->login;
    }
}