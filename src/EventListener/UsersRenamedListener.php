<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Event\UsersRenamedEvent;
use App\Service\Telegram\Notifier;

class UsersRenamedListener
{
    public function __construct(
        private readonly Notifier $notifier,
    ) {
    }

    public function onAppUsersRenamed(UsersRenamedEvent $event): void
    {
        $this->notifier->sendUsersRenamedNotification($event->renames);
    }
}
