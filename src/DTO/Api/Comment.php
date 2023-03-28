<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** TODO: Refactor to public readonly */
class Comment implements ValidableInterface
{
    private ?string $postId;
    private ?int $number;
    private ?int $toCommentId;
    private ?string $created;
    private ?string $text;
    private ?User $author;
    private ?bool $isRec;


    public function getPostId(): ?string
    {
        return $this->postId;
    }

    public function setPostId(?string $postId): void
    {
        $this->postId = $postId;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): void
    {
        $this->number = $number;
    }

    public function getToCommentId(): ?int
    {
        return $this->toCommentId;
    }

    public function setToCommentId(?int $toCommentId): void
    {
        $this->toCommentId = $toCommentId;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function setCreated(?string $created): void
    {
        $this->created = $created;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    public function isIsRec(): ?bool
    {
        return $this->isRec;
    }

    public function getIsRec(): ?bool
    {
        return $this->isRec;
    }

    public function setIsRec(?bool $isRec): void
    {
        $this->isRec = $isRec;
    }

    public function isValid(): bool
    {
        return null !== $this->postId && null !== $this->number && null !== $this->author && null !== $this->text;
    }
}
