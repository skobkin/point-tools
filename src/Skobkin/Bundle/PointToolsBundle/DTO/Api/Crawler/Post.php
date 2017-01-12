<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class Post
{
    /**
     * @var string
     *
     * @JMSS\SerializedName("id")
     * @JMSS\Type("string")
     */
    private $id;

    /**
     * @var string[]
     *
     * @JMSS\SerializedName("tags")
     * @JMSS\Type("array<string>")
     */
    private $tags;

    /**
     * @var string[]
     *
     * @JMSS\SerializedName("files")
     * @JMSS\Type("array<string>")
     */
    private $files;

    /**
     * @var User
     *
     * @JMSS\SerializedName("author")
     * @JMSS\Type("Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\User")
     * @JMSS\MaxDepth(1)
     */
    private $author;

    /**
     * @var string
     *
     * @JMSS\SerializedName("text")
     * @JMSS\Type("string")
     */
    private $text;

    /**
     * @var string
     *
     * @JMSS\SerializedName("created")
     * @JMSS\Type("string")
     */
    private $created;

    /**
     * @var string
     *
     * @JMSS\SerializedName("type")
     * @JMSS\Type("string")
     */
    private $type;

    /**
     * @var bool
     *
     * @JMSS\SerializedName("private")
     * @JMSS\Type("boolean")
     */
    private $private;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param string[] $files
     *
     * @return Post
     */
    public function setFiles(?array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(?bool $private): self
    {
        $this->private = $private;
        return $this;
    }
}