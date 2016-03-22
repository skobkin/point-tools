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
     * @var Recommendation
     *
     * @JMSS\SerializedName("rec")
     * @JMSS\Type("Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\Recommendation")
     * @JMSS\MaxDepth(2)
     */
    private $rec;

    /**
     * @var Post
     *
     * @JMSS\SerializedName("post")
     * @JMSS\Type("Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\Post")
     * @JMSS\MaxDepth(2)
     */
    private $post;


    /**
     * @return Recommendation
     */
    public function getRec()
    {
        return $this->rec;
    }

    /**
     * @param Recommendation $rec
     * @return MetaPost
     */
    public function setRec(Recommendation $rec)
    {
        $this->rec = $rec;
        return $this;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return MetaPost
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
        return $this;
    }
}