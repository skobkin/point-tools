<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class PostsPage
{
    /**
     * @var MetaPost[]
     *
     * @JMSS\SerializedName("posts")
     * @JMSS\Type("array<Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\MetaPost>")
     * @JMSS\MaxDepth(3)
     */
    private $posts;

    /**
     * @return MetaPost[]|ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param MetaPost[]|ArrayCollection $posts
     * @return PostsPage
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
        return $this;
    }


}