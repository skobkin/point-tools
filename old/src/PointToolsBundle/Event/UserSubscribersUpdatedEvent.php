<?php

namespace src\PointToolsBundle\Event;

use src\PointToolsBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched when user subscribers list was changed
 */
class UserSubscribersUpdatedEvent extends Event
{
    const NAME = 'app.user.subscribers_updated';

    /**
     * @var User
     */
    private $user;

    /**
     * @var User[]
     */
    private $subscribed;

    /**
     * @var User[]
     */
    private $unsubscribed;

    /**
     * @param User $user
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    public function __construct(User $user, array $subscribed, array $unsubscribed)
    {
        $this->user = $user;
        $this->subscribed = $subscribed;
        $this->unsubscribed = $unsubscribed;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return User[]
     */
    public function getSubscribedUsers(): array
    {
        return $this->subscribed;
    }

    /**
     * @return User[]
     */
    public function getUnsubscribedUsers(): array
    {
        return $this->unsubscribed;
    }
}