<?php
declare(strict_types=1);

namespace App\Enum\Blog;

enum PostTypeEnum: string
{
    case Post = 'post';
    case Feed = 'feed';
}
