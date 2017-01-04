<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use unreal4u\TelegramAPI\Telegram\Methods\AnswerInlineQuery;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Query;
use unreal4u\TelegramAPI\Telegram\Types\InputMessageContent\Text;
use unreal4u\TelegramAPI\Telegram\Types\Message;
use unreal4u\TelegramAPI\Telegram\Types\Update;
use unreal4u\TelegramAPI\TgLog;

/**
 * @todo refactor
 */
class IncomingUpdateProcessor
{
    const CHAT_TYPE_PRIVATE = 'private';
    const CHAT_TYPE_GROUP = 'group';

    const PARSE_MODE_MARKDOWN = 'Markdown';
    const PARSE_MODE_HTML5 = 'HTML';

    /**
     * @var TgLog
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var int
     */
    private $pointUserId;

    /**
     * @param TgLog $client
     */
    public function __construct(int $pointUserId, TgLog $client, EntityManagerInterface $em, \Twig_Environment $twig)
    {
        $this->client = $client;
        $this->em = $em;
        $this->twig = $twig;
        $this->pointUserId = $pointUserId;
    }

    /**
     * Processes update and delegates it to corresponding service
     *
     * @param Update $update
     */
    public function process(Update $update)
    {
        if ($update->message && $update->message instanceof Message) {
            $chatType = $update->message->chat->type;

            if (self::CHAT_TYPE_PRIVATE === $chatType) {
                $this->processPrivateMessage($update);
            } elseif (self::CHAT_TYPE_GROUP === $chatType) {

            }
        } elseif ($update->inline_query && $update->inline_query instanceof Query) {
            $this->processInlineQuery($update);
        }

    }

    /**
     * @todo refactor
     *
     * @param Update $update
     */
    private function processPrivateMessage(Update $update)
    {
        $chatId = $update->message->chat->id;
        $text = $update->message->text;

        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chatId;
        $sendMessage->parse_mode = self::PARSE_MODE_MARKDOWN;
        $sendMessage->disable_web_page_preview = true;

        $words = explode(' ', $text, 3);

        if (0 === count($words)) {
            return;
        }

        switch ($words[0]) {
            case 'l':
            case '/last':
            case 'last':
                if (array_key_exists(1, $words)) {
                    $sendMessage->text = 'Not implemented yet :(';
                } else {
                    $events = $this->em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getLastSubscriptionEvents(10);
                    $sendMessage->text = $this->twig->render('@SkobkinPointTools/Telegram/last_global_subscriptions.md.twig', ['events' => $events]);
                }

                break;

            case 'sub':
            case '/sub':
            case 'subscribers':
                $sendMessage->text = 'Subscribers list here...';
                break;

            case 'stats':
            case '/stats':
                $stats = [
                    'total_users' => $this->em->getRepository('SkobkinPointToolsBundle:User')->getUsersCount(),
                    'active_users' => $this->em->getRepository('SkobkinPointToolsBundle:Subscription')->getUserSubscribersCountById($this->pointUserId),
                    'today_events' => $this->em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getLastDayEventsCount(),
                ];

                $sendMessage->text = $this->twig->render('@SkobkinPointTools/Telegram/stats.md.twig', $stats);

                break;

            case '/help':
            default:
                $sendMessage->text = $this->twig->render('@SkobkinPointTools/Telegram/help.md.twig');
                break;
        }

        $this->client->performApiRequest($sendMessage);
    }

    private function processInlineQuery(Update $update)
    {
        $queryId = $update->inline_query->id;
        $text = $update->inline_query->query;

        if (mb_strlen($text) < 2) {
            return;
        }

        $answerInlineQuery = new AnswerInlineQuery();
        $answerInlineQuery->inline_query_id = $queryId;

        foreach ($this->em->getRepository('SkobkinPointToolsBundle:User')->findUsersLikeLogin($text) as $user) {
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