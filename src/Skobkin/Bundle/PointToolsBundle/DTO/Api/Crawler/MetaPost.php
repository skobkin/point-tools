<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class MetaPost
{
    /**
     * @var Post
     *
     * @JMSS\SerializedName("post")
     * @JMSS\Type("Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\Post")
     * @JMSS\MaxDepth(2)
     */
    private $post;


    /**
     * @return Post
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}