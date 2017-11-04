<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use unreal4u\TelegramAPI\Telegram\Types\{Inline\Query, Message, Update};

/**
 * Dispatches incoming messages processing to corresponding services
 */
class IncomingUpdateDispatcher
{
    const CHAT_TYPE_PRIVATE = 'private';
    const CHAT_TYPE_GROUP = 'group';

    /**
     * @var InlineQueryProcessor
     */
    private $inlineQueryProcessor;

    /**
     * @var PrivateMessageProcessor
     */
    private $privateMessageProcessor;


    public function __construct(PrivateMessageProcessor $privateMessageProcessor, InlineQueryProcessor $inlineQueryProcessor)
    {
        $this->privateMessageProcessor = $privateMessageProcessor;
        $this->inlineQueryProcessor = $inlineQueryProcessor;
    }

    /**
     * Processes update and delegates it to corresponding service
     *
     * @param Update $update
     */
    public function process(Update $update): void
    {
        if ($update->message && $update->message instanceof Message) {
            $chatType = $update->message->chat->type;

            if (self::CHAT_TYPE_PRIVATE === $chatType) {
                $this->privateMessageProcessor->process($update->message);
            } elseif (self::CHAT_TYPE_GROUP === $chatType) {
                // @todo implement
            }
        } elseif ($update->inline_query && $update->inline_query instanceof Query) {
            $this->inlineQueryProcessor->process($update->inline_query);
        }
    }
}