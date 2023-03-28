<?php
declare(strict_types=1);

namespace App\Factory\Blog;

use App\Factory\{AbstractFactory};
use App\Factory\UserFactory;
use Psr\Log\LoggerInterface;
use App\Repository\Blog\{CommentRepository, PostRepository};

class CommentFactory extends AbstractFactory
{
    public function __construct(
        LoggerInterface $logger,
        private readonly CommentRepository $commentRepository,
        private readonly PostRepository $postRepository,
        private readonly UserFactory $userFactory,
    ) {
        parent::__construct($logger);
    }
}
