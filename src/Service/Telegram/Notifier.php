<?php
declare(strict_types=1);

namespace App\Service\Telegram;

use App\Entity\{User, UserRenameEvent};
use App\Repository\Telegram\AccountRepository;

/**
 * Notifies Telegram users about some events
 */
class Notifier
{
    public function __construct(
        private readonly AccountRepository $accountsRepo,
        private readonly MessageSender $messenger,
    ) {
    }

    /** @param UserRenameEvent[] $userRenameEvents */
    public function sendUsersRenamedNotification(array $userRenameEvents): void
    {
        $accounts = $this->accountsRepo->findBy(['renameNotification' => true]);

        $this->messenger->sendMassTemplatedMessage($accounts, 'Telegram/users_renamed_notification.md.twig', ['events' => $userRenameEvents]);
    }

    /**
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    public function sendUserSubscribersUpdatedNotification(User $user, array $subscribed, array $unsubscribed): bool
    {
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
            'Telegram/user_subscribers_updated_notification.md.twig',
            [
                'user' => $user,
                'subscribed' => $subscribed,
                'unsubscribed' => $unsubscribed,
            ]
        );
    }
}