<?php
namespace src\PointToolsBundle\DTO\Api;

interface ValidableInterface
{
    public function isValid(): bool;
}