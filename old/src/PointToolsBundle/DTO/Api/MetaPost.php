<?php

namespace src\PointToolsBundle\DTO\Api;

use src\PointToolsBundle\DTO\Api\Comment;
use src\PointToolsBundle\DTO\Api\Post;
use src\PointToolsBundle\DTO\Api\ValidableInterface;

class MetaPost implements ValidableInterface
{
    /**
     * @var Post|null
     */
    private $post;

    /**
     * @var Comment[]|null
     */
    private $comments;


    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return Comment[]|null
     */
    public function getComments(): ?array
    {
        return $this->comments;
    }

    /**
     * @param Comment[]|null $comments
     */
    public function setComments(?array $comments): void
    {
        $this->comments = $comments;
    }

    public function isValid(): bool
    {
        if (null !== $this->post && $this->post->isValid()) {
            return true;
        }

        return false;
    }
}