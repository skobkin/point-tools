<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

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
     * @ORM\Column(name="number", type="integer", unique=true)
     */
    private $number;

    /**
     * @var int|null
     *
     * @ORM\Column(name="to_number", type="integer", nullable=true)
     */
    private $toNumber;

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


    public function __construct(
        string $text,
        \DateTime $createdAt,
        bool $rec,
        Post $post,
        int $number,
        ?int $toNumber,
        User $author,
        array $files
    ) {
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->rec = $rec;
        $this->post = $post;
        $this->number = $number;
        $this->toNumber = $toNumber;
        $this->author = $author;

        $this->files = new ArrayCollection();
        foreach ($files as $file) {
            if (!($file instanceof File)) {
                throw new \RuntimeException(sprintf(
                    '$files array must contain only \'%s\' objects. %s given.',
                    \is_object($file) ? \get_class($file) : \gettype($file)
                ));
            }

            $this->files->add($file);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isRec(): bool
    {
        return $this->rec;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getToNumber(): ?int
    {
        return $this->toNumber;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return File[]|ArrayCollection
     */
    public function getFiles(): iterable
    {
        return $this->files;
    }

    public function delete(): self
    {
        $this->deleted = true;

        return $this;
    }

    public function restore(): self
    {
        $this->deleted = false;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
