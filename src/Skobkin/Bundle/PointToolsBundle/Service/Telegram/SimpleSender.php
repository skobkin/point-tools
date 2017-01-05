<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use GuzzleHttp\Exception\ClientException;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;

/**
 * Service which sends simple messages to Telegram users
 */
class SimpleSender
{
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

    /**
     * Send simple message
     *
     * @param int $chatId
     * @param string $text
     *
     * @return bool
     */
    public function sendMessage(int $chatId, string $text): bool
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = (string) $chatId;
        $sendMessage->text = $text;

        try {
            $this->client->performApiRequest($sendMessage);

            return true;
        } catch (ClientException $e) {
            return false;
        }
    }
}