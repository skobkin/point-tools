<?php
declare(strict_types=1);

namespace App\Service\Telegram;

use App\Enum\Telegram\ChatTypeEnum;
use App\Exception\Telegram\CommandProcessingException;
use App\Factory\Telegram\AccountFactory;
use App\Service\Api\UserApi;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Telegram\Account;
use App\Entity\User;
use App\Repository\Telegram\AccountRepository;
use App\Repository\{SubscriptionEventRepository, SubscriptionRepository, UserRepository};
use TelegramBot\Api\Types\{ReplyKeyboardMarkup, ReplyKeyboardRemove, Update};

/** Processes all private messages */
class PrivateMessageProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepo,
        private readonly AccountRepository $accountRepo,
        private readonly SubscriptionRepository $subscriptionRepo,
        private readonly SubscriptionEventRepository $subscriptionEventRepo,
        private readonly MessageSender $messenger,
        private readonly UserApi $userApi,
        private readonly AccountFactory $accountFactory,
        private readonly int $pointAppUserId,
    ) {
    }

    public function process(Update $update): void
    {
        if (!ChatTypeEnum::Private->value === $update->getMessage()->getChat()->getType()) {
            throw new \LogicException('This service can process only private chat messages');
        }

        try {
            $account = $this->accountFactory->findOrCreateFromMessage($update->getMessage());
            $this->em->flush();
        } catch (\Exception $e) {
            $this->messenger->sendMessageToChat(
                $update->getMessage()->getChat()->getId(),
                'There was an error during your Telegram account registration. Try again or report the bug.'
            );

            return;
        }

        try {
            $words = explode(' ', $update->getMessage()?->getText() ?? '', 10);

            if (0 === count($words)) {
                return;
            }

            match ($words[0]) {
                '/link', 'link' => $this->processLink($account, $words),
                '/me', 'me' => $this->processMe($account),
                '/last', 'last' => $this->processLast($account, $words),
                '/sub', 'sub' => $this->processSub($account, $words),
                '/stats', 'stats' => $this->processStats($account),
                '/set' => $this->processSet($account, $words),
                '/exit', 'exit' => $this->processExit($account),
                default => $this->processHelp($account),
            };
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
        if (null === $user = $this->userRepo->findUserByLogin($login)) {
            throw new CommandProcessingException('User not found in Point Tools database. Please try again later.');
        }

        if ($this->userApi->isLoginAndPasswordValid($login, $password)) {
            $account->setUser($user);

            return true;
        }

        return false;
    }

    private function processLink(Account $account, array $words): void
    {
        if (\array_key_exists(2, $words)) {
            if ($this->linkAccount($account, $words[1], $words[2])) {
                $this->em->flush();
                $this->sendAccountLinked($account);
            } else {
                $this->sendError($account, 'Account linking error', 'Check login and password or try again later.');
            }
        } else {
            $this->sendError($account, 'Login/Password error', 'You need to specify login and password separated by space after /link (example: `/link mylogin MypASSw0rd`)');
        }
    }

    private function processMe(Account $account): void
    {
        if ($user = $account->getUser()) {
            $this->sendUserEvents($account, $user);
        } else {
            $this->sendError($account, 'Account not linked', 'You must /link your account first to be able to use this command.');
        }
    }

    private function processLast(Account $account, array $words): void
    {
        if (\array_key_exists(1, $words)) {
            if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                $this->sendUserEvents($account, $user);
            } else {
                $this->sendError($account, 'User not found');
            }
        } else {
            $this->sendGlobalEvents($account);
        }
    }

    private function processSub(Account $account, array $words): void
    {
        if (\array_key_exists(1, $words)) {
            if (null !== $user = $this->userRepo->findUserByLogin($words[1])) {
                $this->sendUserSubscribers($account, $user);
            } else {
                $this->sendError($account, 'User not found');
            }
        } elseif ($user = $account->getUser()) {
            $this->sendUserSubscribers($account, $user);
        } else {
            $this->sendError($account, 'Account not linked', 'You must /link your account first to be able to use this command.');
        }
    }

    private function processStats(Account $account): void
    {
        $this->sendStats($account);
    }

    private function processSet(Account $account, array $words): void
    {
        $keyboard = new ReplyKeyboardMarkup([], true);

        if (\array_key_exists(1, $words)) {
            if (\array_key_exists(2, $words)) {
                if ('renames' === $words[2]) {
                    $account->toggleRenameNotification();
                    $this->em->flush();

                    $this->messenger->sendMessage($account, 'Renaming notifications are turned '.($account->isRenameNotification() ? 'on' : 'off'));
                } elseif ('subscribers' === $words[2]) {
                    $account->toggleSubscriberNotification();
                    $this->em->flush();

                    $this->messenger->sendMessage($account, 'Subscribers notifications are turned '.($account->isSubscriberNotification() ? 'on' : 'off'));

                    if ($account->isSubscriberNotification() && null === $account->getUser()) {
                        $this->messenger->sendMessage($account, 'You need to /link you account to receive these notifications.');
                    }
                } else {
                    $this->sendError($account, 'Notification type does not exist.');
                }
            } else {
                $keyboard->setKeyboard([
                    ['/set notifications renames'],
                    ['/set notifications subscribers'],
                    ['exit'],
                ]);

                $this->messenger->sendMessage($account, 'Choose which notification type to toggle', MessageSender::PARSE_PLAIN, $keyboard);
            }

        } else {
            $keyboard->setKeyboard([
                ['/set notifications'],
                ['exit'],
            ]);

            $this->messenger->sendTemplatedMessage($account, 'Telegram/settings.md.twig', ['account' => $account], $keyboard);
        }
    }

    /**
     * Processes exit from keyboard menus and removes the keyboard
     */
    private function processExit(Account $account): void
    {
        $keyboardRemove = new ReplyKeyboardRemove(true);

        $this->messenger->sendMessage($account, 'Done', MessageSender::PARSE_PLAIN, $keyboardRemove);
    }

    private function processHelp(Account $account): void
    {
        $this->sendHelp($account);
    }

    private function sendAccountLinked(Account $account): void
    {
        $this->messenger->sendMessage($account, 'Account linked. Try using /me now.');
    }

    private function sendUserSubscribers(Account $account, User $user): void
    {
        $subscribers = [];
        foreach ($user->getSubscribers() as $subscription) {
            $subscribers[] = '@'.$subscription->getSubscriber()->getLogin();
        }

        $this->messenger->sendTemplatedMessage(
            $account,
            'Telegram/user_subscribers.md.twig',
            [
                'user' => $user,
                'subscribers' => $subscribers,
            ]
        );
    }

    private function sendUserEvents(Account $account, User $user): void
    {
        $events = $this->subscriptionEventRepo->getUserLastSubscribersEvents($user, 10);

        $this->messenger->sendTemplatedMessage(
            $account,
            'Telegram/last_user_subscriptions.md.twig',
            [
                'user' => $user,
                'events' => $events,
            ]
        );
    }

    private function sendGlobalEvents(Account $account): void
    {
        $events = $this->subscriptionEventRepo->getLastSubscriptionEvents(10);

        $this->messenger->sendTemplatedMessage($account, 'Telegram/last_global_subscriptions.md.twig', ['events' => $events]);
    }

    private function sendStats(Account $account): void
    {
        $this->messenger->sendTemplatedMessage(
            $account,
            'Telegram/stats.md.twig',
            [
                'total_users' => $this->userRepo->getUsersCount(),
                'active_users' => $this->subscriptionRepo->getUserSubscribersCountById($this->pointAppUserId),
                'telegram_accounts' => $this->accountRepo->getAccountsCount(),
                'telegram_linked_accounts' => $this->accountRepo->getLinkedAccountsCount(),
                'today_events' => $this->subscriptionEventRepo->getLastDayEventsCount(),
            ]
        );
    }

    private function sendHelp(Account $account): void
    {
        $this->messenger->sendTemplatedMessage($account, 'Telegram/help.md.twig');
    }

    private function sendError(Account $account, string $title, string $text = ''): void
    {
        $this->messenger->sendTemplatedMessage(
            $account,
            'Telegram/error.md.twig',
            [
                'title' => $title,
                'text' => $text,
            ]
        );
    }
}
