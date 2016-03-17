<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Symfony\Component\Serializer\Annotation as Serializer;

class Recommendation
{
    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $text;

    /**
     * @var int|null
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $comment_id;

    /**
     * @var User
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $author;


    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Recommendation
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getComment_id()
    {
        return $this->comment_id;
    }

    /**
     * @param int|null $comment_id
     * @return Recommendation
     */
    public function setComment_id($comment_id)
    {
        $this->comment_id = $comment_id;
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
     * @return Recommendation
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }
}