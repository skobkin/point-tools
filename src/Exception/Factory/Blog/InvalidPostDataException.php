<?php
declare(strict_types=1);

namespace App\Exception\Factory\Blog;

use App\DTO\Api\Post;

class InvalidPostDataException extends InvalidDataException
{
    public function __construct(
        public readonly Post $post,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct('Invalid post data', $code, $previous);
    }
}
