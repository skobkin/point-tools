<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Exceptions\Factory\Blogs;


use Exception;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\Post;

class InvalidPostDataException extends \Exception
{
    /**
     * @var Post
     */
    private $post;

    public function __construct($message = '', Post $post, $code = 0, Exception $previous = null)
    {
        $this->post = $post;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }
}