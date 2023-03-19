<?php
declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\Blog\Post;
use App\Repository\Blog\PostTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostTagRepository::class)]
#[ORM\Table(name: 'posts_tags', schema: 'posts')]
class PostTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'postTags')]
    #[ORM\JoinColumn(name: 'post_id', onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Tag::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'tag_id')]
    private Tag $tag;

    #[ORM\Column(name: 'text', type: 'text')]
    private string $text;


    public function __construct(Post $post, Tag $tag, string $text)
    {
        $this->post = $post;
        $this->tag = $tag;
        $this->text = $text;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getOriginalTagText(): string
    {
        return  $this->tag->getText();
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}
