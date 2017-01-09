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

    public function __construct(string $login, int $subscribersCount)
    {
        $this->login = $login;
        $this->subscribersCount = $subscribersCount;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getSubscribersCount(): int
    {
        return $this->subscribersCount;
    }
}