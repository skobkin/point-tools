<?php
declare(strict_types=1);

namespace App\Service\Telegram;

use App\Entity\Telegram\Account;
use Psr\Log\LoggerInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use Twig\Environment;

class MessageSender
{
    public const PARSE_PLAIN = '';
    public const PARSE_MARKDOWN = 'Markdown';
    public const PARSE_MARKDOWN_V2 = 'MarkdownV2';
    public const PARSE_HTML = 'HTML';

    public function __construct(
        private readonly BotApi $client,
        private readonly Environment $twig,
        private readonly LoggerInterface $log,
    ) {
    }

    /** @param Account[] $accounts */
    public function sendMassTemplatedMessage(
        array $accounts,
        string $template,
        array $templateData = [],
        ReplyKeyboardMarkup $keyboardMarkup = null,
        bool $disableWebPreview = true,
        bool $disableNotifications = false,
        string $parseMode = self::PARSE_MARKDOWN_V2
    ): void
    {
        $text = $this->twig->render($template, $templateData);

        foreach ($accounts as $account) {
            $this->sendMessage($account, $text, $parseMode, $keyboardMarkup, $disableWebPreview, $disableNotifications);
        }
    }

    public function sendTemplatedMessage(
        Account $account,
        string $template,
        array $templateData = [],
        ReplyKeyboardMarkup $keyboardMarkup = null,
        bool $disableWebPreview = true,
        bool $disableNotifications = false,
        string $parseMode = self::PARSE_MARKDOWN_V2
    ): bool
    {
        $text = $this->twig->render($template, $templateData);

        return $this->sendMessage($account, $text, $parseMode, $keyboardMarkup, $disableWebPreview, $disableNotifications);
    }

    public function sendMessage(
        Account $account,
        string $text,
        string $parseMode = self::PARSE_PLAIN,
        ReplyKeyboardMarkup $keyboardMarkup = null,
        bool $disableWebPreview = false,
        bool $disableNotifications = false
    ): bool
    {
        return $this->sendMessageToChat($account->getChatId(), $text, $parseMode, $keyboardMarkup, $disableWebPreview, $disableNotifications);
    }

    public function sendMessageToChat(
        int $chatId,
        string $text,
        string $parseMode = self::PARSE_PLAIN,
        ReplyKeyboardMarkup $keyboardMarkup = null,
        bool $disableWebPreview = false,
        bool $disableNotifications = false
    ): bool
    {
        try {
            $this->client->sendMessage(
                chatId: (string) $chatId,
                text: $text,
                parseMode: $parseMode,
                disablePreview: $disableWebPreview,
                replyMarkup: $keyboardMarkup,
                disableNotification: $disableNotifications,
            );

            return true;
        } catch (\Exception $e) {
            $this->log->error('sendMessageToChat', [
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);

            return false;
        }
    }
}
