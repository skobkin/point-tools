<?php

namespace Skobkin\Bundle\PointToolsBundle\Exception\Api;

class PostNotFoundException extends NotFoundException
{
    /** @var string */
    private $id;

    public function __construct(string $id, \Exception $previous)
    {
        parent::__construct('Post not found', 0, $previous);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}