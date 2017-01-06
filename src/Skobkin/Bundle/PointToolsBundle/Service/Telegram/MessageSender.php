<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use GuzzleHttp\Exception\ClientException;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Types\ReplyKeyboardMarkup;
use unreal4u\TelegramAPI\TgLog;

/**
 * Service which sends simple messages to Telegram users
 */
class MessageSender
{
    const PARSE_MODE_NOPARSE = '';
    const PARSE_MODE_MARKDOWN = 'Markdown';
    const PARSE_MODE_HTML5 = 'HTML';

    /**
     * @var TgLog
     */
    private $client;

    /**
     * @param TgLog $client
     */
    public function __construct(TgLog $client)
    {
        $this->client = $client;
    }

    public function sendMessageToUser(
        Account $account,
        string $text,
        string $parseMode = self::PARSE_MODE_NOPARSE,
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
        string $parseMode = self::PARSE_MODE_NOPARSE,
        ReplyKeyboardMarkup $keyboardMarkup = null,
        bool $disableWebPreview = false,
        bool $disableNotifications = false
    ): bool
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = (string) $chatId;
        $sendMessage->text = $text;
        $sendMessage->parse_mode = $parseMode;
        $sendMessage->disable_web_page_preview = $disableWebPreview;
        $sendMessage->disable_notification = $disableNotifications;
        $sendMessage->reply_markup = $keyboardMarkup;

        try {
            $this->client->performApiRequest($sendMessage);

            return true;
        } catch (ClientException $e) {
            return false;
        }
    }
}