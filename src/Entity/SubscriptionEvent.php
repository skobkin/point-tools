<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionEventRepository::class, readOnly: true)]
#[ORM\Table(name: 'log', schema: 'subscriptions')]
#[ORM\Index(columns: ['author_id'], name: 'idx_subscription_author')]
#[ORM\Index(columns: ['subscriber_id'], name: 'idx_subscription_subscriber')]
#[ORM\Index(columns: ['date'], name: 'idx_subscription_date')]
class SubscriptionEvent
{
    public const ACTION_SUBSCRIBE = 'subscribe';
    public const ACTION_UNSUBSCRIBE = 'unsubscribe';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'newSubscriberEvents')]
    #[ORM\JoinColumn(name: 'author_id', nullable: false)]
    private User $author;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'subscriber_id', nullable: false)]
    private User $subscriber;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false)]
    private \DateTime $date;

    #[ORM\Column(name: 'action', type: 'string', length: 12, nullable: false)]
    private string $action;


    public function __construct(User $author, User $subscriber, string $action = self::ACTION_SUBSCRIBE)
    {
        $this->author = $author;
        $this->subscriber = $subscriber;
        $this->action = $action;
        $this->date = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getSubscriber(): User
    {
        return $this->subscriber;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
