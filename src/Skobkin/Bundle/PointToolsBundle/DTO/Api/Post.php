<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api;

class Post implements ValidableInterface
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string[]|null
     */
    private $tags;

    /**
     * @var string[]|null
     */
    private $files;

    /**
     * @var User|null
     */
    private $author;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @var string|null
     */
    private $created;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var bool|null
     */
    private $private;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return string[]|null
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param string[]|null $files
     */
    public function setFiles(?array $files): void
    {
        $this->files = $files;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): void
    {
        $this->created = $created;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(?bool $private): void
    {
        $this->private = $private;
    }

    public function isValid(): bool
    {
        if (
            null !== $this->id &&
            null !== $this->author &&
            $this->author->isValid() &&
            null !== $this->text &&
            null !== $this->created &&
            null !== $this->type
        ) {
            return true;
        }

        return false;
    }
}