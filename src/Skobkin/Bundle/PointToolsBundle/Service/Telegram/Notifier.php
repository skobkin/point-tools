<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Entity\UserRenameEvent;

/**
 * Notifies Telegram users about some events
 */
class Notifier
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $accountsRepo;

    /**
     * @var MessageSender
     */
    private $messenger;


    /**
     * Notifier constructor.
     *
     * @param EntityManagerInterface $em
     * @param MessageSender $messenger
     */
    public function __construct(EntityManagerInterface $em, MessageSender $messenger)
    {
        $this->em = $em;
        $this->messenger = $messenger;

        $this->accountsRepo = $em->getRepository('SkobkinPointToolsBundle:Telegram\Account');
    }

    /**
     * @param UserRenameEvent[] $userRenameEvents
     */
    public function sendUsersRenamedNotification(array $userRenameEvents)
    {
        $accounts = $this->accountsRepo->findBy(['renameNotification' => true]);

        $this->messenger->sendMassTemplatedMessage($accounts, '@SkobkinPointTools/Telegram/users_renamed_notification.md.twig', ['events' => $userRenameEvents]);
    }

    /**
     * Send notification about changes in user's subscribers list
     *
     * @param User $user
     * @param array $subscribed
     * @param array $unsubscribed
     */
    public function sendUserSubscribersUpdatedNotification(User $user, array $subscribed, array $unsubscribed)
    {
        $account = $this->accountsRepo->findOneBy(
            [
                'user' => $user,
                'subscriberNotification' => true,
            ]
        );

        if (null === $account) {
            return;
        }

        $this->messenger->sendTemplatedMessage(
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