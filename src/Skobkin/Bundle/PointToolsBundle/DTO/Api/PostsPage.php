<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

class PostsPage implements ValidableInterface
{
    /**
     * @var MetaPost[]|null
     */
    private $posts;

    /**
     * @var bool|null
     */
    private $hasNext;

    /**
     * @return MetaPost[]|null
     */
    public function getPosts(): ?array
    {
        return $this->posts;
    }

    /**
     * @param MetaPost[]|null $posts
     */
    public function setPosts(?array $posts): void
    {
        $this->posts = $posts;
    }

    public function getHasNext(): ?bool
    {
        return $this->hasNext;
    }

    public function setHasNext(?bool $hasNext): void
    {
        $this->hasNext = $hasNext;
    }

    public function isValid(): bool
    {
        if (null !== $this->posts) {
            return true;
        }

        return false;
    }
}