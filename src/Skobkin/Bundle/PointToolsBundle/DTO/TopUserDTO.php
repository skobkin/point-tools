<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO;

/**
 * Data Transfer Object for top users list
 */
class TopUserDTO
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var int
     */
    private $subscribersCount;

    public function __construct($login, $subscribersCount)
    {
        $this->login = $login;
        $this->subscribersCount = $subscribersCount;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return int
     */
    public function getSubscribersCount()
    {
        return $this->subscribersCount;
    }
}