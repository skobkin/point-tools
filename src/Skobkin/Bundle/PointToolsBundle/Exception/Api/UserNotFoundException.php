<?php

namespace Skobkin\Bundle\PointToolsBundle\Exception\Api;

class UserNotFoundException extends NotFoundException
{
    /** @var int */
    private $userId;

    /** @var string */
    private $login;

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