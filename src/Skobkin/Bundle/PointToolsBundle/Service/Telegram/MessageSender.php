<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use GuzzleHttp\Exception\ClientException;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use unreal4u\TelegramAPI\Abstracts\KeyboardMethods;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;

/**
 * Service which sends simple messages to Telegram users
 */
class MessageSender
{
    public const PARSE_PLAIN = '';
    public const PARSE_MARKDOWN = 'Markdown';
    public const PARSE_HTML5 = 'HTML';

    /** @var TgLog */
    private $client;

    /** @var \Twig_Environment */
    private $twig;

    /**
     * @param TgLog $client
     */
    public function __construct(TgLog $client, \Twig_Environment $twig)
    {
        $this->client = $client;
        $this->twig = $twig;
    }

    /**
     * @param Account[] $accounts
     * @param string $template
     * @param array $templateData
     * @param KeyboardMethods|null $keyboardMarkup
     * @param bool $disableWebPreview
     * @param bool $disableNotifications
     * @param string $parseMode
     */
    public function sendMassTemplatedMessage(
        array $accounts,
        string $template,
        array $templateData = [],
        KeyboardMethods $keyboardMarkup = null,
        bool $disableWebPreview = true,
        bool $disableNotifications = false,
        string $parseMode = self::PARSE_MARKDOWN
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
        KeyboardMethods $keyboardMarkup = null,
        bool $disableWebPreview = true,
        bool $disableNotifications = false,
        string $parseMode = self::PARSE_MARKDOWN
    ): bool
    {
        $text = $this->twig->render($template, $templateData);

        return $this->sendMessage($account, $text, $parseMode, $keyboardMarkup, $disableWebPreview, $disableNotifications);
    }

    public function sendMessage(
        Account $account,
        string $text,
        string $parseMode = self::PARSE_PLAIN,
        KeyboardMethods $keyboardMarkup = null,
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
        KeyboardMethods $keyboardMarkup = null,
        bool $disableWebPreview = false,
        bool $disableNotifications = false
    ): bool
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = (string)$chatId;
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