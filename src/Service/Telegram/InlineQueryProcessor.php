<?php
declare(strict_types=1);

namespace App\Service\Telegram;

use App\Repository\UserRepository;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineQuery;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;

class InlineQueryProcessor
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly BotApi $client,
    ) {
    }

    public function process(InlineQuery $inlineQuery): void
    {
        $text = $inlineQuery->getQuery();

        if (\mb_strlen($text) < 2) {
            return;
        }

        $results = [];

        foreach ($this->userRepo->findUsersLikeLogin($text) as $key => $user) {
            $results[] = new Article(
                id: \hash('md5', (string) $user->getId()),
                title: $user->getLogin(),
                inputMessageContent: new Text(
                    messageText: \sprintf(
                        "@%s:\nName: %s\nSubscribers: %d",
                        $user->getLogin(),
                        $user->getName(),
                        $user->getSubscribers()->count()
                    ),
                    parseMode: MessageSender::PARSE_PLAIN,
                    disableWebPagePreview: true,
                ),
            );
        }

        $this->client->answerInlineQuery(
            $inlineQuery->getId(),
            $results,
        );
    }
}
