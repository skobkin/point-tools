<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

class MetaPost implements ValidableInterface
{
    /** @var Post|null */
    private $post;

    /** @var Comment[]|null */
    private $comments;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * @return Comment[]|null
     */
    public function getComments(): ?array
    {
        return $this->comments;
    }

    public function isValid(): bool
    {
        return (null !== $this->post && $this->post->isValid());
    }
}