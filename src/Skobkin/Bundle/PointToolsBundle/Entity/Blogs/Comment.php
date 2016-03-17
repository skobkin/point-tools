<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Comment
 *
 * @ORM\Table(name="posts.comments", schema="posts", indexes={
 *      @ORM\Index(name="idx_comment_created_at", columns={"created_at"})
 * })
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var integer
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\Column(name="is_rec", type="boolean")
     */
    private $rec;

    /**
     * @var bool
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var Post
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id")
     */
    private $post;

    /**
     * @var User
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id")
     */
    private $author;

    /**
     * @var Comment|null
     *
     * @Serializer\Groups("post_show")
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment")
     * @ORM\JoinColumn(name="to_comment_id", nullable=true)
     */
    private $toComment;

    /**
     * @param int $id
     * @param Post $post
     * @param User $author
     * @param Comment|null $toComment
     * @param string $text
     * @param \DateTime $createdAt
     * @param bool $isRec
     */
    public function __construct($id, Post $post, User $author, Comment $toComment = null, $text, \DateTime $createdAt, $isRec)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }
        if (!is_bool($isRec)) {
            throw new \InvalidArgumentException('$isRec must be boolean');
        }
        if (!is_string($text)) {
            throw new \InvalidArgumentException('$text must be a string');
        }

        $this->id = (int)$id;
        $this->post = $post;
        $this->author = $author;
        $this->toComment = $toComment;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->rec = $isRec;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set isRec
     *
     * @param boolean $rec
     * @return Comment
     */
    public function setRec($rec)
    {
        $this->rec = $rec;

        return $this;
    }

    /**
     * Get isRec
     *
     * @return boolean 
     */
    public function isRec()
    {
        return $this->rec;
    }

    /**
     * Get isRec
     *
     * @return boolean
     */
    public function getRec()
    {
        return $this->rec;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return Comment
     */
    public function setPost($post)
    {
        $this->post = $post;

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
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Comment
     */
    public function getToComment()
    {
        return $this->toComment;
    }

    /**
     * @param Comment $toComment
     * @return Comment
     */
    public function setToComment($toComment)
    {
        $this->toComment = $toComment;

        return $this;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Comment
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }
}
