<?php

namespace Skobkin\Bundle\PointToolsBundle\EventListener;


use Skobkin\Bundle\PointToolsBundle\Service\Telegram\Notifier;
use Symfony\Component\EventDispatcher\GenericEvent;

class UsersRenameNotifierListener
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

    public function onAppUsersRenamed(GenericEvent $event)
    {
        $this->notifier->sendUsersRenamedNotification((array) $event->getIterator());
    }
}