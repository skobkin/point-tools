<?php

namespace Skobkin\Bundle\PointToolsBundle\EventListener;

use Skobkin\Bundle\PointToolsBundle\Event\UsersRenamedEvent;
use Skobkin\Bundle\PointToolsBundle\Service\Telegram\Notifier;

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