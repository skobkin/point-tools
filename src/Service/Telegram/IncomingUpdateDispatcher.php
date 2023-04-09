<?php
declare(strict_types=1);

namespace App\Service\Telegram;

use App\Enum\Telegram\ChatTypeEnum;
use Psr\Log\LoggerInterface;
use TelegramBot\Api\Types\Update;

/** Dispatches incoming messages processing to corresponding services */
class IncomingUpdateDispatcher
{
    public function __construct(
        private readonly PrivateMessageProcessor $privateMessageProcessor,
        private readonly InlineQueryProcessor $inlineQueryProcessor,
        private readonly LoggerInterface $log,
    ) {
    }

    public function process(Update $update): void
    {
        if ($update->getMessage() && $update->getMessage()->getText()) {
            $chatType = $update->getMessage()->getChat()->getType();
            match (ChatTypeEnum::tryFrom($chatType)) {
                ChatTypeEnum::Private => $this->privateMessageProcessor->process($update),
                default => $this->log->info(\sprintf(
                    'Unsupported message type \'%s\' received.\n  %s\n  %s',
                    $chatType,
                    $update->getMessage()->getChat()->getId(),
                    $update->getMessage()->getText(),
                ))
            };
        } elseif ($update->getInlineQuery()) {
            $this->inlineQueryProcessor->process($update->getInlineQuery());
        }
    }
}
