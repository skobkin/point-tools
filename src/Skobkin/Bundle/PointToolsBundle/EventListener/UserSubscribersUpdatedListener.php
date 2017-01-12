<?php

namespace Skobkin\Bundle\PointToolsBundle\EventListener;

use Skobkin\Bundle\PointToolsBundle\Event\UserSubscribersUpdatedEvent;
use Skobkin\Bundle\PointToolsBundle\Service\Telegram\Notifier;

class UserSubscribersUpdatedListener
{
    /**
     * @var Notifier
     */
    private $notifier;


    /**
     * UsersRenameNotifierListener constructor.
     *
     * @param Notifier $notifier
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function onAppUserSubscribersUpdated(UserSubscribersUpdatedEvent $event): void
    {
        $this->notifier->sendUserSubscribersUpdatedNotification($event->getUser(), $event->getSubscribedUsers(), $event->getUnsubscribedUsers());
    }
}