<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** TODO: Refactor to public readonly */
class PostsPage implements ValidableInterface
{
    /** @var MetaPost[]|null */
    private ?array $posts;
    private ?bool $hasNext;

    /** @return MetaPost[]|null */
    public function getPosts(): ?array
    {
        return $this->posts;
    }

    /** @param MetaPost[]|null $posts */
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
        return null !== $this->posts;
    }
}
