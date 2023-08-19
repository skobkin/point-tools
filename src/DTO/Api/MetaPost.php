<?php
declare(strict_types=1);

namespace App\DTO\Api;

use Symfony\Component\Serializer\Annotation\{MaxDepth, SerializedName};

class MetaPost implements ValidableInterface
{
    /**
     * @param Comment[] $comments
     */
    public function __construct(
        #[SerializedName('post')]
        #[MaxDepth(2)]
        public readonly ?Post $post,
        #[SerializedName('comments')]
        #[MaxDepth(2)]
        public readonly ?array $comments,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->post && $this->post->isValid();
    }
}
