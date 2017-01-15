<?php
namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

interface ValidableInterface
{
    public function isValid(): bool;
}