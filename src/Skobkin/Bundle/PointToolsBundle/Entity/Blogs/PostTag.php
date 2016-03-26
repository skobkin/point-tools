<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostTag
 *
 * @ORM\Table(name="posts.posts_tags", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostTagRepository")
 */
class PostTag
{
    /**
     * @var integer
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


    /**
     * PostTag constructor.
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
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
     * @return PostTag
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
     * @return string
     */
    public function getOriginalTagText()
    {
        return $this->tag ? $this->tag->getText() : '';
    }

    /**
     * Set post
     *
     * @param Post $post
     * @return PostTag
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set tag
     *
     * @param Tag $tag
     * @return PostTag
     */
    public function setTag(Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}
