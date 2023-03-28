<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** TODO: Refactor to public readonly */
class MetaPost implements ValidableInterface
{
    private ?Post $post;
    /** @var Comment[]|null */
    private ?array $comments;


    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    /** @return Comment[]|null */
    public function getComments(): ?array
    {
        return $this->comments;
    }

    /** @param Comment[]|null $comments */
    public function setComments(?array $comments): void
    {
        $this->comments = $comments;
    }

    public function isValid(): bool
    {
        return null !== $this->post && $this->post->isValid();
    }
}
