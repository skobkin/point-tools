<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use unreal4u\TelegramAPI\Telegram\Methods\AnswerInlineQuery;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Query;
use unreal4u\TelegramAPI\Telegram\Types\InputMessageContent\Text;
use unreal4u\TelegramAPI\TgLog;

class InlineQueryProcessor
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var TgLog
     */
    private $client;


    public function __construct(UserRepository $userRepository, TgLog $client)
    {
        $this->userRepo = $userRepository;
        $this->client = $client;
    }

    public function process(Query $inlineQuery): void
    {
        if (mb_strlen($inlineQuery->query) < 2) {
            return;
        }

        $answerInlineQuery = new AnswerInlineQuery();
        $answerInlineQuery->inline_query_id = $inlineQuery->id;

        foreach ($this->userRepo->findUsersLikeLogin($inlineQuery->query) as $user) {
            $article = new Query\Result\Article();
            $article->title = $user->getLogin();

            $contentText = new Text();
            $contentText->message_text = sprintf(
                "@%s:\nName: %s\nSubscribers: %d",
                $user->getLogin(),
                $user->getName(),
                $user->getSubscribers()->count()
            );

            $article->input_message_content = $contentText;
            $article->id = md5($user->getId());

            $answerInlineQuery->addResult($article);
        }

        $this->client->performApiRequest($answerInlineQuery);
    }
}