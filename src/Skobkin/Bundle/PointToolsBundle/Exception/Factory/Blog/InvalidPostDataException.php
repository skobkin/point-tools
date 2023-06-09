<?php

namespace Skobkin\Bundle\PointToolsBundle\Exception\Factory\Blog;

use Skobkin\Bundle\PointToolsBundle\DTO\Api\Post;

class InvalidPostDataException extends InvalidDataException
{
    /**
     * @var Post
     */
    private $post;

    public function __construct($message = '', Post $post, $code = 0, \Exception $previous = null)
    {
        $this->post = $post;

        parent::__construct($message, $code, $previous);
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}