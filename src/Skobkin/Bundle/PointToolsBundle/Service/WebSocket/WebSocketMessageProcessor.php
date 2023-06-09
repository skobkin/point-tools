<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\WebSocket;

use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\{CommentFactory, PostFactory};

class WebSocketMessageProcessor
{
    /** @var PostFactory */
    private $postFactory;

    /** @var CommentFactory */
    private $commentFactory;

    public function __construct(PostFactory $postFactory, CommentFactory $commentFactory)
    {
        $this->postFactory = $postFactory;
        $this->commentFactory = $commentFactory;
    }

    /**
     * Returns true on success (all data saved)
     */
    public function processMessage(Message $message): bool
    {
        if (!$message->isValid()) {
            return false;
        }

        switch (true) {
            case $message->isPost():
                return $this->processPost($message);
                break;

            case $message->isComment():
                return $this->processComment($message);
                break;

            case $message->isCommentRecommendation():
                return $this->processRecommendation($message);
                break;
        }

        return false;
    }

    private function processPost(Message $postData): bool
    {
        $this->postFactory->findOrCreateFromWebsocketDto($postData);

        return true;
    }

    private function processComment(Message $commentData): bool
    {
        // Not done yet
        return false;

        $this->commentFactory->findOrCreateFromWebsocketMessage($commentData);

        return true;
    }

    private function processRecommendation(Message $recommendData): bool
    {
        return false;
    }
}