<?php
declare(strict_types=1);

namespace App\DTO\Api;

class PostsPage implements ValidableInterface
{
    /**
     * @param MetaPost[]|null $posts
     */
    public function __construct(
        public readonly ?array $posts,
        public readonly ?bool $hasNext,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->posts;
    }
}
