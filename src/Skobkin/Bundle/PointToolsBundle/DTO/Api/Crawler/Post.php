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


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Post
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     * @return Post
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param string[] $files
     *
     * @return Post
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Post
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     * @return Post
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @return boolean
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @param boolean $private
     * @return Post
     */
    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }
}