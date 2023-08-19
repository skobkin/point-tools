<?php
declare(strict_types=1);

namespace App\DTO\Api;

use App\Enum\Blog\PostTypeEnum;
use Symfony\Component\Serializer\Annotation\{Context, MaxDepth, SerializedName};
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class Post implements ValidableInterface
{
    public function __construct(
        #[SerializedName('id')]
        public readonly ?string $id,
        #[SerializedName('tags')]
        public readonly ?array $tags,
        #[SerializedName('files')]
        public readonly ?array $files,
        #[SerializedName('author')]
        #[MaxDepth(1)]
        public readonly ?User $author,
        #[SerializedName('text')]
        public readonly ?string $text,
        #[SerializedName('created')]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d_H:i:s'])]
        public readonly ?\DateTimeImmutable $created,
        #[SerializedName('type')]
        public readonly ?PostTypeEnum $type,
        #[SerializedName('private')]
        public readonly ?bool $private,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->id &&
            null !== $this->author &&
            $this->author->isValid() &&
            null !== $this->text &&
            null !== $this->created
            // @todo check type existence in incoming data
            //null !== $this->type
        ;
    }
}