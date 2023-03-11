<?php

namespace src\PointToolsBundle\Entity\Blogs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use src\PointToolsBundle\Entity\Blogs\File;
use src\PointToolsBundle\Entity\Blogs\PostTag;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Entity\Blogs\Comment;

/**
 * @ORM\Table(name="posts", schema="posts", indexes={
 *      @ORM\Index(name="idx_post_created_at", columns={"created_at"}),
 *      @ORM\Index(name="idx_post_private", columns={"private"}),
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    public const TYPE_POST = 'post';
    public const TYPE_FEED = 'feed';

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="text")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=6)
     */
    private $type = self::TYPE_POST;

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
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User")
     * @ORM\JoinColumn(name="author")
     */
    private $author;

    /**
     * @var File[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\File", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinTable(name="posts_files", schema="posts",
     *     joinColumns={@ORM\JoinColumn(name="post_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="file_id")}
     * )
     */
    private $files;

    /**
     * @var PostTag[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\PostTag", mappedBy="post", fetch="EXTRA_LAZY", cascade={"persist"}, orphanRemoval=true)
     */
    private $postTags;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment", mappedBy="post", cascade={"persist"})
     */
    private $comments;


    public function __construct(string $id, User $author, \DateTime $createdAt, string $type)
    {
        $this->id = $id;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->type = $type;

        $this->files = new ArrayCollection();
        $this->postTags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function addFile(File $files): self
    {
        $this->files[] = $files;

        return $this;
    }

    public function removeFile(File $files): void
    {
        $this->files->removeElement($files);
    }

    /**
     * @return File[]|ArrayCollection
     */
    public function getFiles(): iterable
    {
        return $this->files;
    }

    public function addPostTag(PostTag $tag): self
    {
        $this->postTags[] = $tag;

        return $this;
    }

    public function removePostTag(PostTag $tag): void
    {
        $this->postTags->removeElement($tag);
    }

    /**
     * @return PostTag[]|ArrayCollection
     */
    public function getPostTags(): iterable
    {
        return $this->postTags;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function getPrivate(): bool
    {
        return $this->private;
    }

    public function addComment(Comment $comment): self
    {
        $this->comments[] = $comment;
        $comment->setPost($this);

        return $this;
    }

    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments(): iterable
    {
        return $this->comments;
    }
}
