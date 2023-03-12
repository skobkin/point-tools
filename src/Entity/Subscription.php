<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class, readOnly: true)]
#[ORM\Table(name: 'subscriptions', schema: 'subscriptions')]
class Subscription
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subscribers')]
    #[ORM\JoinColumn(name: 'author_id')]
    private User $author;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(name: 'subscriber_id')]
    private User $subscriber;

    public function __construct(User $author, User $subscriber)
    {
        $this->author = $author;
        $this->subscriber = $subscriber;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getSubscriber(): User
    {
        return $this->subscriber;
    }
}
