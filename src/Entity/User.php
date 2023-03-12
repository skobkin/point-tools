<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users', schema: 'users')]
#[ORM\Index(columns: ['public'], name: 'idx_user_public')]
#[ORM\Index(columns: ['is_removed'], name: 'idx_user_removed')]
#[ORM\HasLifecycleCallbacks]
class User
{
    public const AVATAR_SIZE_SMALL = '24';
    public const AVATAR_SIZE_MEDIUM = '40';
    public const AVATAR_SIZE_LARGE = '80';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'login', type: 'string', length: 255, nullable: false)]
    private string $login;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private \DateTime $updatedAt;

    #[ORM\Column(name: 'public', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $public = false;

    #[ORM\Column(name: 'whitelist_only', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $whitelistOnly = false;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Subscription::class, fetch: 'EXTRA_LAZY')]
    private ArrayCollection $subscribers;

    #[ORM\OneToMany(mappedBy: 'subscriber', targetEntity: Subscription::class, fetch: 'EXTRA_LAZY')]
    private ArrayCollection $subscriptions;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: SubscriptionEvent::class, fetch: 'EXTRA_LAZY')]
    private ArrayCollection $newSubscriberEvents;

    #[ORM\Column(name: 'is_removed', type: 'boolean', options: ['default' => false])]
    private bool $removed = false;

    public function __construct(
        int $id,
        \DateTime $createdAt = null,
        string $login = null,
        string $name = null
    ) {
        $this->id = $id;
        $this->login = $login;
        $this->name = $name;
        $this->createdAt = $createdAt ?: new \DateTime();

        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->newSubscriberEvents = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function updateLoginAndName(string $login, ?string $name): self
    {
        $this->login = $login;
        $this->name = $name;

        return $this;
    }

    public function addSubscriber(Subscription $subscribers): self
    {
        $this->subscribers[] = $subscribers;

        return $this;
    }

    public function removeSubscriber(Subscription $subscribers)
    {
        $this->subscribers->removeElement($subscribers);
    }

    /** @return Subscription[]|ArrayCollection */
    public function getSubscribers(): ArrayCollection
    {
        return $this->subscribers;
    }

    /** @return Subscription[]|ArrayCollection */
    public function getSubscriptions(): ArrayCollection
    {
        return $this->subscriptions;
    }

    public function addNewSubscriberEvent(SubscriptionEvent $newSubscriberEvents): self
    {
        $this->newSubscriberEvents[] = $newSubscriberEvents;

        return $this;
    }

    /** @return SubscriptionEvent[]|ArrayCollection */
    public function getNewSubscriberEvents(): ArrayCollection
    {
        return $this->newSubscriberEvents;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function updatePrivacy(?bool $public, ?bool $whitelistOnly): void
    {
        $this->public = $public;
        $this->whitelistOnly = $whitelistOnly;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function isWhitelistOnly(): bool
    {
        return $this->whitelistOnly;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function markAsRemoved(): void
    {
        $this->removed = true;
    }

    public function restore(): void
    {
        $this->removed = false;
    }
}