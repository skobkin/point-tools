<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Exception\Telegram\CommandProcessingException;
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionEventRepository;
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionRepository;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Telegram\AccountFactory;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Types\Message;
use unreal4u\TelegramAPI\TgLog;

/**
 * Processes all private messages
 */
class PrivateMessageProcessor
{
    const TEMPLATE_ERROR = '@SkobkinPointTools/Telegram/error.md.twig';
    const TEMPLATE_STATS = '@SkobkinPointTools/Telegram/stats.md.twig';
    const TEMPLATE_HELP = '@SkobkinPointTools/Telegram/help.md.twig';
    const TEMPLATE_LAST_EVENTS = '@SkobkinPointTools/Telegram/last_global_subscriptions.md.twig';
    const TEMPLATE_LAST_USER_SUB_EVENTS = '@SkobkinPointTools/Telegram/last_user_subscriptions.md.twig';
    const TEMPLATE_USER_SUBSCRIBERS = '@SkobkinPointTools/Telegram/user_subscribers.md.twig';

    const PARSE_MODE_MARKDOWN = 'Markdown';
    const PARSE_MODE_HTML5 = 'HTML';

    /**
     * @var TgLog
     */
    private $client;

    /**
     * @var UserApi
     */
    private $userApi;

    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepo;

    /**
     * @var SubscriptionEventRepository
     */
    private $subscriptionEventRepo;

    /**
     * @var int
     */
    private $pointUserId;


    public function __construct(
        TgLog $client,
        UserApi $userApi,
        AccountFactory $accountFactory,
        EntityManagerInterface $em,
        \Twig_Environment $twig,
        int $pointUserId
    )
    {
        $this->client = $client;
        $this->userApi = $userApi;
        $this->accountFactory = $accountFactory;
        $this->em = $em;
        $this->twig = $twig;
        $this->pointUserId = $pointUserId;

        $this->userRepo = $em->getRepository('SkobkinPointToolsBundle:User');
        $this->subscriptionRepo = $em->getRepository('SkobkinPointToolsBundle:Subscription');
        $this->subscriptionEventRepo = $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent');
    }

    public function process(Message $message)
    {
        if (!IncomingUpdateDispatcher::CHAT_TYPE_PRIVATE === $message->chat->type) {
            throw new \InvalidArgumentException('This service can process only private chat messages');
        }

        // Registering Telegram user
        /** @var Account $account */
        $account = $this->accountFactory->findOrCreateFromMessage($message);
        $this->em->flush();

        // Creating blank response for later use
        $sendMessage = $this->createResponseMessage($message, self::PARSE_MODE_MARKDOWN, true);

        $words = explode(' ', $message->text, 4);

        if (0 === count($words)) {
            return;
        }

        try {
            switch ($words[0]) {
                case '/link':
                case 'link':
                    if (array_key_exists(2, $words)) {
                        if ($this->linkAccount($account, $words[1], $words[2])) {
                            // Saving linking status
                            $this->em->flush();
                            $this->sendAccountLinked($sendMessage);
                        } else {
                            $this->sendError($sendMessage, 'Account linking error', 'Check login and password or try again later.');
                        }
                    } else {
                        $this->sendError($sendMessage, 'Login/Password error', 'You need to specify login and password separated by space after /link (example: `/link mylogin MypASSw0rd`)');
                    }
                    break;

                case '/me':
                case 'me':
                    if ($user = $account->getUser()) {
                        $this->sendUserEvents($sendMessage, $user);
                    } else {
                        $this->sendError($sendMessage, 'Account not linked', 'You must /link your account first to be able to use this command.');
                    }
                    break;

                case '/last':
                case 'l':
                case 'last':
                    if (array_key_exists(1, $words)) {
                        if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                            $this->sendUserEvents($sendMessage, $user);
                        } else {
                            $this->sendError($sendMessage, 'User not found');
                        }
                    } else {
                        $this->sendGlobalEvents($sendMessage);
                    }

                    break;

                case '/sub':
                case 'sub':
                    if (array_key_exists(1, $words)) {
                        if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                            $this->sendUserSubscribers($sendMessage, $user);
                        } else {
                            $this->sendError($sendMessage, 'User not found');
                        }
                    } else {
                        if ($user = $account->getUser()) {
                            $this->sendUserSubscribers($sendMessage, $user);
                        } else {
                            $this->sendError($sendMessage, 'Account not linked', 'You must /link your account first to be able to use this command.');
                        }
                    }

                    break;

                case '/stats':
                case 'stats':
                    $this->sendStats($sendMessage);

                    break;

                case '/help':
                default:
                    $this->sendHelp($sendMessage);
                    break;
            }
        } catch (CommandProcessingException $e) {
            $this->sendError($sendMessage, 'Processing error', $e->getMessage());

            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
        } catch (\Exception $e) {
            $this->sendError($sendMessage, 'Unknown error');

            throw $e;
        }
    }

    private function linkAccount(Account $account, string $login, string $password): bool
    {
        /** @var User $user */
        if (null === $user = $this->userRepo->findUserByLogin($login)) {
            throw new CommandProcessingException('User not found in Point Tools database. Please try again later.');
        }

        if ($this->userApi->isAuthDataValid($login, $password)) {
            $account->setUser($user);

            return true;
        }

        return false;
    }

    private function sendAccountLinked(SendMessage $sendMessage)
    {
        $sendMessage->text = 'Account linked. Try using /me now.';

        $this->client->performApiRequest($sendMessage);
    }

    private function sendUserSubscribers(SendMessage $sendMessage, User $user)
    {
        $subscribers = [];

        foreach ($user->getSubscribers() as $subscription) {
            $subscribers[] = '@'.$subscription->getSubscriber()->getLogin();
        }

        $sendMessage->text = $this->twig->render(self::TEMPLATE_USER_SUBSCRIBERS, [
            'user' => $user,
            'subscribers' => $subscribers,
        ]);

        $this->client->performApiRequest($sendMessage);
    }

    /**
     * @param SendMessage $sendMessage
     * @param User $user
     */
    private function sendUserEvents(SendMessage $sendMessage, User $user)
    {
        $events = $this->subscriptionEventRepo->getUserLastSubscribersEvents($user, 10);
        $sendMessage->text = $this->twig->render(self::TEMPLATE_LAST_USER_SUB_EVENTS, [
            'user' => $user,
            'events' => $events,
        ]);

        $this->client->performApiRequest($sendMessage);
    }

    private function sendGlobalEvents(SendMessage $sendMessage)
    {
        $events = $this->subscriptionEventRepo->getLastSubscriptionEvents(10);
        $sendMessage->text = $this->twig->render(self::TEMPLATE_LAST_EVENTS, ['events' => $events]);

        $this->client->performApiRequest($sendMessage);
    }

    private function sendStats(SendMessage $sendMessage)
    {
        $sendMessage->text = $this->twig->render(self::TEMPLATE_STATS, [
            'total_users' => $this->userRepo->getUsersCount(),
            'active_users' => $this->subscriptionRepo->getUserSubscribersCountById($this->pointUserId),
            'today_events' => $this->subscriptionEventRepo->getLastDayEventsCount(),
        ]);

        $this->client->performApiRequest($sendMessage);
    }

    private function sendHelp(SendMessage $sendMessage)
    {
        $sendMessage->text = $this->twig->render(self::TEMPLATE_HELP);

        $this->client->performApiRequest($sendMessage);
    }

    private function sendError(SendMessage $sendMessage, string $title, string $text = '')
    {
        $sendMessage->text = $this->twig->render(self::TEMPLATE_ERROR, [
            'title' => $title,
            'text' => $text,
        ]);

        $this->client->performApiRequest($sendMessage);
    }

    private function createResponseMessage(Message $message, string $parseMode = self::PARSE_MODE_MARKDOWN, bool $disableWebPreview = false): SendMessage
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = (string) $message->chat->id;
        $sendMessage->parse_mode = $parseMode;
        $sendMessage->disable_web_page_preview = $disableWebPreview;

        return $sendMessage;
    }
}