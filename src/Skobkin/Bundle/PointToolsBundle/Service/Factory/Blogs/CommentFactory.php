<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\CommentRepository;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\AbstractFactory;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\UserFactory;

class CommentFactory extends AbstractFactory
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var UserFactory
     */
    private $userFactory;


    public function __construct(LoggerInterface $logger, CommentRepository $commentRepository, PostRepository $postRepository, UserFactory $userFactory)
    {
        parent::__construct($logger);
        $this->userFactory = $userFactory;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }
}