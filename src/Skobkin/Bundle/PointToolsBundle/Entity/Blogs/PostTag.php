<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posts_tags", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostTagRepository", readOnly=true)
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
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postTags")
     * @ORM\JoinColumn(name="post_id", onDelete="CASCADE")
     */
    private $post;

    /**
     * @var Tag
     *
     * @ORM\ManyToOne(targetEntity="Tag", fetch="EAGER")
     * @ORM\JoinColumn(name="tag_id")
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    public function __construct(Post $post, Tag $tag, string $text)
    {
        $this->post = $post;
        $this->tag = $tag;
        $this->text = $text;
    }

    public function getId(): int
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
