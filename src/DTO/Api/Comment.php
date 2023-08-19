<?php
declare(strict_types=1);

namespace App\DTO\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Comment implements ValidableInterface
{
    public function __construct(
        #[SerializedName('post_id')]
        public readonly ?string $postId,
        #[SerializedName('id')]
        public readonly ?int $number,
        #[SerializedName('to_comment_id')]
        public readonly ?int $toCommentId,
        #[SerializedName('created')]
        public readonly ?\DateTimeImmutable $created,
        #[SerializedName('text')]
        public readonly ?string $text,
        #[SerializedName('author')]
        public readonly ?User $author,
        #[SerializedName('is_rec')]
        public readonly ?bool $isRec,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->postId && null !== $this->number && null !== $this->author && null !== $this->text;
    }
}
