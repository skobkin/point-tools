<?php
declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\User;
use App\Enum\Blog\PostTypeEnum;
use App\Repository\Blog\PostRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'posts', schema: 'posts')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'text')]
    private string $id;

    #[ORM\Column(name: 'text', type: 'text')]
    private string $text;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\Column(name: 'type', type: 'string', length: 6, enumType: PostTypeEnum::class)]
    private PostTypeEnum $type;

    #[ORM\Column(name: 'private', type: 'boolean', nullable: true)]
    private bool $private;

    #[ORM\Column(name: 'is_deleted', type: 'boolean')]
    private bool $deleted = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author')]
    private User $author;

    /** @var Collection<int, File> */
    #[ORM\ManyToMany(targetEntity: File::class, cascade: ['persist'], fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'posts_files', schema: 'posts')]
    #[ORM\JoinColumn(name: 'post_id')]
    #[ORM\InverseJoinColumn(name: 'file_id')]
    private Collection $files;

    /** @var Collection<int, PostTag> */
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostTag::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $postTags;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $comments;


    public function __construct(string $id, User $author, \DateTimeImmutable $createdAt, PostTypeEnum $type)
    {
        $this->id = $id;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->type = $type;

        $this->files = new ArrayCollection();
        $this->postTags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[ORM\PreUpdate]
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getType(): PostTypeEnum
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

    /** @return Collection<int, File> */
    public function getFiles(): Collection
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

    /** @return Collection<int, PostTag> */
    public function getPostTags(): Collection
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

    /** @return Collection<int, Comment> */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
