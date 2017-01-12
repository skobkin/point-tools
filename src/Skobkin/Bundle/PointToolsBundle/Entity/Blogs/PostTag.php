<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posts_tags", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostTagRepository")
 */
class PostTag
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post", inversedBy="postTags")
     * @ORM\JoinColumn(name="post_id", onDelete="CASCADE")
     */
    private $post;

    /**
     * @var Tag
     *
     * @todo fix SET NULL
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag", fetch="EAGER")
     * @ORM\JoinColumn(name="tag_id", onDelete="SET NULL")
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getId(): int
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

    public function getOriginalTagText(): string
    {
        return $this->tag ? $this->tag->getText() : '';
    }

    /**
     * Set post
     *
     * @todo move to constructor
     *
     * @param Post $post
     * @return PostTag
     */
    public function setPost(Post $post = null): self
    {
        $this->post = $post;

        return $this;
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
