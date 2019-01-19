<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\{CommentRepository, PostRepository};
use Skobkin\Bundle\PointToolsBundle\Service\Factory\{AbstractFactory, UserFactory};

class CommentFactory extends AbstractFactory
{
    /** @var CommentRepository */
    private $commentRepository;

    /** @var PostRepository */
    private $postRepository;

    /** @var UserFactory */
    private $userFactory;


    public function __construct(LoggerInterface $logger, CommentRepository $commentRepository, PostRepository $postRepository, UserFactory $userFactory)
    {
        parent::__construct($logger);
        $this->userFactory = $userFactory;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    public function findOrCreateFromWebsocketMessage(Message $message): Comment
    {
        if ($message->isValid()) {
            throw new \InvalidArgumentException('Comment is invalid');
        }
        if ($message->isComment()) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid Message object provided. %s expected, %s given',
                Message::TYPE_COMMENT,
                $message->getA()
            ));
        }


    }
}