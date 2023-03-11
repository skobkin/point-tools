<?php

namespace src\PointToolsBundle\EventListener;

use src\PointToolsBundle\Event\UsersRenamedEvent;
use src\PointToolsBundle\Service\Telegram\Notifier;

class UsersRenamedListener
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

    public function onAppUsersRenamed(UsersRenamedEvent $event): void
    {
        $this->notifier->sendUsersRenamedNotification($event->getRenames());
    }
}