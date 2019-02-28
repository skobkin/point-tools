<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\{CommentRepository, PostRepository};
use Skobkin\Bundle\PointToolsBundle\Service\Api\PostApi;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\{AbstractFactory, UserFactory};

class CommentFactory extends AbstractFactory
{
    /** @var CommentRepository */
    private $commentRepository;

    /** @var PostRepository */
    private $postRepository;

    /** @var UserFactory */
    private $userFactory;

    /** @var PostApi */
    private $postApi;


    public function __construct(LoggerInterface $logger, CommentRepository $commentRepository, PostRepository $postRepository, UserFactory $userFactory, PostApi $postApi)
    {
        parent::__construct($logger);
        $this->userFactory = $userFactory;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->postApi = $postApi;
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

        if (null === $comment = $this->commentRepository->findOneBy(['post' => $post, 'number' => $message->getCommentId()])) {
            $author = $this->userFactory->findOrCreateFromIdLoginAndName(
                $message->getAuthorId(),
                $message->getAuthor(),
                $message->getAuthorName()
            );

            if (null === $post = $this->postRepository->find($message->getPostId())) {
                $post = $this->postApi->getById($message->getPostId());
            }

            // TODO
            //$comment = new Comment()
        }
    }
}