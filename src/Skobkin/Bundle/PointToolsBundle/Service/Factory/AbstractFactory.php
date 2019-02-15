<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;

abstract class AbstractFactory
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}