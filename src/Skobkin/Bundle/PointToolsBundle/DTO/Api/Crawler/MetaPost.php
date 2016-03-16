<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Symfony\Component\Serializer\Annotation as Serializer;

class MetaPost
{
    /**
     * @var Recommendation
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $rec;

    /**
     * @var Post
     *
     * @Serializer\Groups({"import_post_page"})
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