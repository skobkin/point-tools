<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket;

use Skobkin\Bundle\PointToolsBundle\DTO\Api\ValidableInterface;
use Skobkin\Bundle\PointToolsBundle\Exception\WebSocket\UnsupportedTypeException;

/**
 * WebSocket update message
 */
class Message implements ValidableInterface
{
    public const TYPE_COMMENT = 'comment';
    public const TYPE_POST = 'post';
    public const TYPE_POST_RECOMMENDATION = 'rec';
    public const TYPE_COMMENT_RECOMMENDATION = 'ok';

    /**
     * Event type. @see Message::TYPE_* constants
     *
     * @var string
     */
    private $a;

    /**
     * Login of the user
     *
     * @var string
     */
    private $author;

    /** @var string|null */
    private $authorName;

    /**
     * Number of the comment in the thread
     *
     * @var int|null
     */
    private $commentId;

    /**
     * ???
     *
     * @var bool|null
     */
    private $cut;

    /**
     * Array of file paths
     *
     * @var string[]|null
     */
    private $files;

    /** @var string|null */
    private $html;

    /**
     * @deprecated Link in the Post::type=feed posts
     *
     * @var string|null
     */
    private $link;

    /** @var string */
    private $postId;

    /** @var bool|null */
    private $private;

    /**
     * Number of the comment in the thread for recommendation with text
     *
     * @var int|null
     */
    private $rcid;

    /**
     * Array of tags
     *
     * @var string[]|null
     */
    private $tags;

    /** @var string */
    private $text;

    /**
     * @deprecated ???
     *
     * @var string|null
     */
    private $title;

    /**
     * Number of the comment to which this comment is answering
     *
     * @var string|null
     */
    private $toCommentId;

    /**
     * Text quotation of the comment to which this comment is answering
     *
     * @var string|null
     */
    private $toText;

    /**
     * Array of logins of users to which post is addressed
     *
     * @var string[]|null
     */
    private $toUsers;

    public function isPost(): bool
    {
        return self::TYPE_POST === $this->a;
    }

    public function isComment(): bool
    {
        return self::TYPE_COMMENT === $this->a;
    }

    public function isCommentRecommendation(): bool
    {
        return self::TYPE_COMMENT_RECOMMENDATION === $this->a;
    }

    public function isPostRecommendation(): bool
    {
        return self::TYPE_POST_RECOMMENDATION === $this->a;
    }

    /**
     * @throws \RuntimeException
     * @throws UnsupportedTypeException
     */
    public function isValid(): bool
    {
        switch ($this->a) {
            case self::TYPE_POST:
                if (
                    null !== $this->author &&
                    null !== $this->html &&
                    null !== $this->postId &&
                    null !== $this->private &&
                    null !== $this->tags
                ) {
                    return true;
                }
                break;

            case self::TYPE_COMMENT;
                if (
                    null !== $this->author &&
                    null !== $this->commentId &&
                    null !== $this->html &&
                    null !== $this->postId &&
                    null !== $this->text
                ) {
                    return true;
                }
                break;

            case self::TYPE_COMMENT_RECOMMENDATION;
                if (
                    null !== $this->author &&
                    null !== $this->postId
                ) {
                    return true;
                }
                break;

            case null:
                throw new \RuntimeException('Message has NULL type.');
                break;

            default:
                throw new UnsupportedTypeException(sprintf('Type \'%s\' is not supported.', $this->a));
        }

        return false;
    }

    public function getA(): string
    {
        return $this->a;
    }

    public function setA(string $a): void
    {
        $this->a = $a;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): void
    {
        $this->authorName = $authorName;
    }

    public function getCommentId(): ?int
    {
        return $this->commentId;
    }

    public function setCommentId(?int $commentId): void
    {
        $this->commentId = $commentId;
    }

    public function getCut(): ?bool
    {
        return $this->cut;
    }

    public function setCut(?bool $cut): void
    {
        $this->cut = $cut;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(?array $files): void
    {
        $this->files = $files;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): void
    {
        $this->html = $html;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function getPostId(): string
    {
        return $this->postId;
    }

    public function setPostId(string $postId): void
    {
        $this->postId = $postId;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(?bool $private): void
    {
        $this->private = $private;
    }

    public function getRcid(): ?int
    {
        return $this->rcid;
    }

    public function setRcid(?int $rcid): void
    {
        $this->rcid = $rcid;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getToCommentId(): ?string
    {
        return $this->toCommentId;
    }

    public function setToCommentId(?string $toCommentId): void
    {
        $this->toCommentId = $toCommentId;
    }

    public function getToText(): ?string
    {
        return $this->toText;
    }

    public function setToText(?string $toText): void
    {
        $this->toText = $toText;
    }

    public function getToUsers(): ?array
    {
        return $this->toUsers;
    }

    public function setToUsers(?array $toUsers): void
    {
        $this->toUsers = $toUsers;
    }
}