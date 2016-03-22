<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class Recommendation
{
    /**
     * @var string
     *
     * @JMSS\SerializedName("text")
     * @JMSS\Type("string")
     */
    private $text;

    /**
     * @var int|null
     *
     * @JMSS\SerializedName("comment_id")
     * @JMSS\Type("integer")
     */
    private $commentId;

    /**
     * @var User
     *
     * @JMSS\SerializedName("author")
     * @JMSS\Type("Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\User")
     * @JMSS\MaxDepth(1)
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
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @param int|null $commentId
     * @return Recommendation
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
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