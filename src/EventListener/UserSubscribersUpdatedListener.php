<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Event\UserSubscribersUpdatedEvent;
use App\Service\Telegram\Notifier;

class UserSubscribersUpdatedListener
{
    public function __construct(
        private readonly Notifier $notifier,
    ) {
    }

    public function onAppUserSubscribersUpdated(UserSubscribersUpdatedEvent $event): void
    {
        $this->notifier->sendUserSubscribersUpdatedNotification($event->user, $event->subscribed, $event->unsubscribed);
    }
}