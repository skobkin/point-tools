<?php

namespace src\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\{
    src\PointToolsBundle\Repository\Blogs\CommentRepository, src\PointToolsBundle\Repository\Blogs\PostRepository};
use src\PointToolsBundle\Service\Factory\{AbstractFactory};
use src\PointToolsBundle\Service\Factory\UserFactory;

class CommentFactory extends AbstractFactory
{
    /** @var \src\PointToolsBundle\Repository\Blogs\CommentRepository */
    private $commentRepository;

    /** @var \src\PointToolsBundle\Repository\Blogs\PostRepository */
    private $postRepository;

    /** @var UserFactory */
    private $userFactory;


    public function __construct(LoggerInterface $logger, \src\PointToolsBundle\Repository\Blogs\CommentRepository $commentRepository, \src\PointToolsBundle\Repository\Blogs\PostRepository $postRepository, UserFactory $userFactory)
    {
        parent::__construct($logger);
        $this->userFactory = $userFactory;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }
}