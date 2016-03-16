<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Symfony\Component\Serializer\Annotation as Serializer;

class PostsPage
{
    /**
     * @var MetaPost[]
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $posts;

    /**
     * @return MetaPost[]
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param MetaPost[] $posts
     * @return PostsPage
     */
    public function setPosts(array $posts)
    {
        $this->posts = $posts;
        return $this;
    }


}