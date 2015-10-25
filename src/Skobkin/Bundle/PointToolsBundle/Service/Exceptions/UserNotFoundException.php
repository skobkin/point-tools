<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Exceptions;


class UserNotFoundException extends ApiException
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

    /**
     * Returns ID of user which was not found
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
}