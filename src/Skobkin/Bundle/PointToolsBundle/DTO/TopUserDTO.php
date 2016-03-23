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
    public $login;

    /**
     * @var int
     */
    public $subscribersCount;

    public function __construct($login, $subscribersCount)
    {
        $this->login = $login;
        $this->subscribersCount = $subscribersCount;
    }
}