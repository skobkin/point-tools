<?php

namespace src\PointToolsBundle\Entity\Blogs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use src\PointToolsBundle\Entity\Blogs\File;
use src\PointToolsBundle\Entity\Blogs\Post;
use src\PointToolsBundle\Entity\User;

/**
 * @ORM\Table(name="comments", schema="posts", indexes={
 *      @ORM\Index(name="idx_comment_created_at", columns={"created_at"})
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
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
     * @var boolean
     *
     * @ORM\Column(name="is_rec", type="boolean")
     */
    private $rec;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id")
     */
    private $post;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="smallint")
     */
    private $number;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="author_id")
     */
    private $author;

    /**
     * @var File[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\File", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinTable(name="comments_files", schema="posts",
     *     joinColumns={@ORM\JoinColumn(name="comment_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="file_id")}
     * )
     */
    private $files;

    /**
     * @var Comment|null
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", nullable=true)
     */
    private $parent;

    /**
     * @var Comment[]|ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment", fetch="EXTRA_LAZY", mappedBy="parent")
     */
    private $children;


    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
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

    public function setRec(bool $rec): self
    {
        $this->rec = $rec;

        return $this;
    }

    public function isRec(): bool
    {
        return $this->rec;
    }

    public function getRec(): bool
    {
        return $this->rec;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
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

    public function getParent(): ?Comment
    {
        return $this->parent;
    }

    public function setParent(Comment $parent): self
    {
        $this->parent = $parent;

        return $this;
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

    public function addChild(Comment $children): self
    {
        $this->children[] = $children;

        return $this;
    }

    public function removeChild(Comment $children): void
    {
        $this->children->removeElement($children);
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }
}
