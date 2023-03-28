<?php
declare(strict_types=1);

namespace App\Factory;

use Psr\Log\LoggerInterface;

abstract class AbstractFactory
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }
}
