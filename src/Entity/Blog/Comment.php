<?php
declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\User;
use App\Repository\Blog\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments', schema: 'posts')]
#[ORM\Index(columns: ['created_at'], name: 'idx_comment_created_at')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(name: 'text', type: 'text')]
    private string $text;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'is_rec', type: 'boolean')]
    private bool $rec = false;

    #[ORM\Column(name: 'is_deleted', type: 'boolean')]
    private bool $deleted = false;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id')]
    private Post $post;

    #[ORM\Column(name: 'number', type: 'smallint')]
    private int $number;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'author_id')]
    private User $author;

    /** @var Collection<int, File> */
    #[ORM\ManyToMany(targetEntity: File::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'comments_files', schema: 'posts')]
    #[ORM\JoinColumn(name: 'comment_id')]
    #[ORM\InverseJoinColumn(name: 'file_id')]
    private Collection $files;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', nullable: true)]
    private ?self $parent;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, fetch: 'EXTRA_LAZY')]
    private Collection $children;


    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
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

    /** @return Collection<int, File> */
    public function getFiles(): Collection
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

    /** @return Collection<int, self> */
    public function getChildren(): Collection
    {
        return $this->children;
    }
}
