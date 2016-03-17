<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Post
 *
 * @ORM\Table(name="posts.posts", schema="posts", indexes={
 *      @ORM\Index(name="idx_post_created_at", columns={"created_at"}),
 *      @ORM\Index(name="idx_post_private", columns={"private"}),
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\PostRepository")
 */
class Post
{
    const TYPE_POST = 'post';
    const TYPE_FEED = 'feed';

    /**
     * @var int
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\Column(name="id", type="string", length=16)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\Column(name="type", type="string", length=6)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="private", type="boolean", nullable=true)
     */
    private $private;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var User
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User")
     * @ORM\JoinColumn(name="author")
     */
    private $author;

    /**
     * @var Tag[]|ArrayCollection
     *
     * @Serializer\Groups({"posts_list", "post_show"})
     *
     * @ORM\ManyToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="posts.posts_tags",
     *      joinColumns={@ORM\JoinColumn(name="post_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id")}
     * )
     */
    private $tags;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @Serializer\Groups({"post_show"})
     *
     * @ORM\OneToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment", mappedBy="post")
     */
    private $comments;


    /**
     * Post constructor.
     * @param string $id
     * @param string $type
     * @param bool $private
     * @param string $text
     * @param \DateTime $createdAt
     * @param User|null $author
     */
    public function __construct($id, $type, $private, $text, \DateTime $createdAt, User $author = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->private = $private;
        $this->createdAt = $createdAt;
        $this->text = $text;
        $this->author = $author;

        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Set text
     *
     * @param string $text
     * @return Post
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
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
     * Set type
     *
     * @param string $type
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
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
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Add tags
     *
     * @param Tag $tag
     * @return Post
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Post
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

    /**
     * Set private
     *
     * @param boolean $private
     * @return Post
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Add comments
     *
     * @param Comment $comment
     * @return Post
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
