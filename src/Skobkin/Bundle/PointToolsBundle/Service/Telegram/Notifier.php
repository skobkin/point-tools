<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use Skobkin\Bundle\PointToolsBundle\Entity\{Telegram\Account, User, UserRenameEvent};
use Skobkin\Bundle\PointToolsBundle\Repository\Telegram\AccountRepository;

/**
 * Notifies Telegram users about some events
 */
class Notifier
{
    /** @var AccountRepository */
    private $accountsRepo;

    /** @var MessageSender */
    private $messenger;


    public function __construct(AccountRepository $accountRepository, MessageSender $messenger)
    {
        $this->accountsRepo = $accountRepository;
        $this->messenger = $messenger;
    }

    /**
     * @param UserRenameEvent[] $userRenameEvents
     */
    public function sendUsersRenamedNotification(array $userRenameEvents): void
    {
        $accounts = $this->accountsRepo->findBy(['renameNotification' => true]);

        $this->messenger->sendMassTemplatedMessage($accounts, '@SkobkinPointTools/Telegram/users_renamed_notification.md.twig', ['events' => $userRenameEvents]);
    }

    /**
     * Send notification about changes in user's subscribers list
     *
     * @param User $user
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    public function sendUserSubscribersUpdatedNotification(User $user, array $subscribed, array $unsubscribed): bool
    {
        /** @var Account $account */
        $account = $this->accountsRepo->findOneBy(
            [
                'user' => $user,
                'subscriberNotification' => true,
            ]
        );

        if (null === $account) {
            return false;
        }

        return $this->messenger->sendTemplatedMessage(
            $account,
            '@SkobkinPointTools/Telegram/user_subscribers_updated_notification.md.twig',
            [
                'user' => $user,
                'subscribed' => $subscribed,
                'unsubscribed' => $unsubscribed,
            ]
        );
    }
}