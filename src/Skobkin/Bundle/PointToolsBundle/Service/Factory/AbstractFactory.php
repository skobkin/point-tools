<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;

abstract class AbstractFactory
{
    public const DATE_FORMAT = 'Y-m-d_H:i:s';

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}