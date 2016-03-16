<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Symfony\Component\Serializer\Annotation as Serializer;

class Post
{
    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $id;

    /**
     * @var string[]
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $tags;

    /**
     * @var User
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $author;

    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $text;

    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $created;

    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $type;

    /**
     * @var bool
     *
     * @Serializer\Groups({"import_post_page"})
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
    public function setTags(array $tags)
    {
        $this->tags = $tags;
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