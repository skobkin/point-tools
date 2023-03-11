<?php

namespace src\PointToolsBundle\EventListener;

use src\PointToolsBundle\Event\UserSubscribersUpdatedEvent;
use src\PointToolsBundle\Service\Telegram\Notifier;

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