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
use unreal4u\TelegramAPI\Abstracts\KeyboardMethods;
use unreal4u\TelegramAPI\Telegram\Types\Message;
use unreal4u\TelegramAPI\Telegram\Types\ReplyKeyboardMarkup;
use unreal4u\TelegramAPI\Telegram\Types\ReplyKeyboardRemove;

/**
 * Processes all private messages
 */
class PrivateMessageProcessor
{
    /**
     * @var MessageSender
     */
    private $messenger;

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
        MessageSender $messageSender,
        UserApi $userApi,
        AccountFactory $accountFactory,
        EntityManagerInterface $em,
        \Twig_Environment $twig,
        int $pointUserId
    )
    {
        $this->messenger = $messageSender;
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
            throw new \LogicException('This service can process only private chat messages');
        }

        try {
            // Registering Telegram user
            /** @var Account $account */
            $account = $this->accountFactory->findOrCreateFromMessage($message);
            $this->em->flush();
        } catch (\Exception $e) {
            // Low-level message in case of incorrect $account
            $this->messenger->sendMessageToChat($message->chat->id, 'There was an error during your Telegram account registration. Try again or report the bug.');
        }

        try {
            $words = explode(' ', $message->text, 10);

            if (0 === count($words)) {
                return;
            }

            switch ($words[0]) {
                case '/link':
                case 'link':
                    $this->processLink($account, $words);
                    break;

                case '/me':
                case 'me':
                    $this->processMe($account);
                    break;

                case '/last':
                case 'last':
                    $this->processLast($account, $words);
                    break;

                case '/sub':
                case 'sub':
                    $this->processSub($account, $words);
                    break;

                case '/stats':
                case 'stats':
                    $this->processStats($account);
                    break;

                // Settings menu
                case '/set':
                    $this->processSet($account, $words);
                    break;

                // Exit from any menu and remove keyboard
                case '/exit':
                case 'exit':
                    $this->processExit($account);
                    break;

                case '/help':
                default:
                    $this->processHelp($account);
                    break;
            }
        } catch (CommandProcessingException $e) {
            $this->sendError($account, 'Processing error', $e->getMessage());

            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
        } catch (\Exception $e) {
            $this->sendError($account, 'Unknown error');

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

    private function processLink(Account $account, array $words)
    {
        if (array_key_exists(2, $words)) {
            if ($this->linkAccount($account, $words[1], $words[2])) {
                // Saving linking status
                $this->em->flush();
                $this->sendAccountLinked($account);
            } else {
                $this->sendError($account, 'Account linking error', 'Check login and password or try again later.');
            }
        } else {
            $this->sendError($account, 'Login/Password error', 'You need to specify login and password separated by space after /link (example: `/link mylogin MypASSw0rd`)');
        }
    }

    private function processMe(Account $account)
    {
        if ($user = $account->getUser()) {
            $this->sendUserEvents($account, $user);
        } else {
            $this->sendError($account, 'Account not linked', 'You must /link your account first to be able to use this command.');
        }
    }

    private function processLast(Account $account, array $words)
    {
        if (array_key_exists(1, $words)) {
            if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                $this->sendUserEvents($account, $user);
            } else {
                $this->sendError($account, 'User not found');
            }
        } else {
            $this->sendGlobalEvents($account);
        }
    }

    private function processSub(Account $account, array $words)
    {
        if (array_key_exists(1, $words)) {
            if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                $this->sendUserSubscribers($account, $user);
            } else {
                $this->sendError($account, 'User not found');
            }
        } else {
            if ($user = $account->getUser()) {
                $this->sendUserSubscribers($account, $user);
            } else {
                $this->sendError($account, 'Account not linked', 'You must /link your account first to be able to use this command.');
            }
        }
    }

    private function processStats(Account $account)
    {
        $this->sendStats($account);
    }

    private function processSet(Account $account, array $words)
    {
        $keyboard = new ReplyKeyboardMarkup();

        if (array_key_exists(1, $words)) {
            if (array_key_exists(2, $words)) {
                if ('renames' === $words[2]) {
                    $account->toggleRenameNotification();
                    $this->em->flush();

                    $this->sendPlainTextMessage($account, 'Renaming notifications are turned '.($account->isRenameNotification() ? 'on' : 'off'));
                } elseif ('subscribers' === $words[2]) {
                    $account->toggleSubscriberNotification();
                    $this->em->flush();

                    $this->sendPlainTextMessage($account, 'Subscribers notifications are turned '.($account->isSubscriberNotification() ? 'on' : 'off'));
                } else {
                    $this->sendError($account, 'Notification type does not exist.');
                }
            } else {
                $keyboard->keyboard = [
                    ['/set notifications renames'],
                    ['/set notifications subscribers'],
                    ['exit'],
                ];

                $this->sendPlainTextMessage($account, 'Choose which notification type to toggle', $keyboard);
            }

        } else {
            $keyboard->keyboard = [
                ['/set notifications'],
                ['exit'],
            ];

            $this->sendTemplatedMessage($account, '@SkobkinPointTools/Telegram/settings.md.twig', ['account' => $account], $keyboard);
        }
    }

    /**
     * Processes exit from keyboard menus and removes the keyboard
     */
    private function processExit(Account $account)
    {
        $keyboardRemove = new ReplyKeyboardRemove();

        $this->sendPlainTextMessage($account, 'Done', $keyboardRemove);
    }

    private function processHelp(Account $account)
    {
        $this->sendHelp($account);
    }

    private function sendAccountLinked(Account $account)
    {
        $this->messenger->sendMessageToUser($account, 'Account linked. Try using /me now.');
    }

    private function sendUserSubscribers(Account $account, User $user)
    {
        $subscribers = [];
        foreach ($user->getSubscribers() as $subscription) {
            $subscribers[] = '@'.$subscription->getSubscriber()->getLogin();
        }

        $this->sendTemplatedMessage(
            $account,
            '@SkobkinPointTools/Telegram/user_subscribers.md.twig',
            [
                'user' => $user,
                'subscribers' => $subscribers,
            ]
        );
    }

    private function sendUserEvents(Account $account, User $user)
    {
        $events = $this->subscriptionEventRepo->getUserLastSubscribersEvents($user, 10);

        $this->sendTemplatedMessage(
            $account,
            '@SkobkinPointTools/Telegram/last_user_subscriptions.md.twig',
            [
                'user' => $user,
                'events' => $events,
            ]
        );
    }

    private function sendGlobalEvents(Account $account)
    {
        $events = $this->subscriptionEventRepo->getLastSubscriptionEvents(10);

        $this->sendTemplatedMessage($account, '@SkobkinPointTools/Telegram/last_global_subscriptions.md.twig', ['events' => $events]);
    }

    private function sendStats(Account $account)
    {
        $this->sendTemplatedMessage(
            $account,
            '@SkobkinPointTools/Telegram/stats.md.twig',
            [
                'total_users' => $this->userRepo->getUsersCount(),
                'active_users' => $this->subscriptionRepo->getUserSubscribersCountById($this->pointUserId),
                'today_events' => $this->subscriptionEventRepo->getLastDayEventsCount(),
            ]
        );
    }

    private function sendHelp(Account $account)
    {
        $this->sendTemplatedMessage($account, '@SkobkinPointTools/Telegram/help.md.twig');
    }

    private function sendError(Account $account, string $title, string $text = '')
    {
        $this->sendTemplatedMessage(
            $account,
            '@SkobkinPointTools/Telegram/error.md.twig',
            [
                'title' => $title,
                'text' => $text,
            ]
        );
    }

    private function sendTemplatedMessage(
        Account $account,
        string $template,
        array $templateData = [],
        KeyboardMethods $keyboardMarkup = null,
        bool $disableWebPreview = true,
        string $format = MessageSender::PARSE_MODE_MARKDOWN
    ): bool
    {
        $text = $this->twig->render($template, $templateData);

        return $this->messenger->sendMessageToUser($account, $text, $format, $keyboardMarkup, $disableWebPreview, false);
    }

    private function sendPlainTextMessage(Account $account, string $text, KeyboardMethods $keyboardMarkup = null, bool $disableWebPreview = true): bool
    {
        return $this->messenger->sendMessageToUser($account, $text, MessageSender::PARSE_MODE_NOPARSE, $keyboardMarkup, $disableWebPreview);
    }
}