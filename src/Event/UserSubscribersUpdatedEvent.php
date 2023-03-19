<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/** Dispatched when user subscribers list was changed */
class UserSubscribersUpdatedEvent extends Event
{
    const NAME = 'app.user.subscribers_updated';

    /**
     * @param User $user
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    public function __construct(
        public readonly User $user,
        public readonly array $subscribed,
        public readonly array $unsubscribed
    ) {
    }
}
