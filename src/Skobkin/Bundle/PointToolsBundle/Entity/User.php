<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users", schema="users", indexes={
 *      @ORM\Index(name="idx_user_login", columns={"login"}),
 *      @ORM\Index(name="idx_user_public", columns={"public"}),
 *      @ORM\Index(name="idx_user_removed", columns={"is_removed"})
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    public const AVATAR_SIZE_SMALL = '24';
    public const AVATAR_SIZE_MEDIUM = '40';
    public const AVATAR_SIZE_LARGE = '80';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", nullable=false, options={"default": false})
     */
    private $public = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="whitelist_only", type="boolean", nullable=false, options={"default": false})
     */
    private $whitelistOnly = false;

    /**
     * @var ArrayCollection|Subscription[]
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $subscribers;

    /**
     * @var ArrayCollection|Subscription[]
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="subscriber", fetch="EXTRA_LAZY")
     */
    private $subscriptions;

    /**
     * @var ArrayCollection|SubscriptionEvent[]
     *
     * @ORM\OneToMany(targetEntity="SubscriptionEvent", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $newSubscriberEvents;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_removed", type="boolean", options={"default": false})
     */
    private $removed = false;


    public function __construct(int $id, \DateTime $createdAt = null, string $login = null, string $name = null)
    {
        $this->id = $id;
        $this->login = $login;
        $this->name = $name;
        $this->createdAt = $createdAt ?: new \DateTime();

        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->newSubscriberEvents = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
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

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscribers(): iterable
    {
        return $this->subscribers;
    }

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscriptions(): iterable
    {
        return $this->subscriptions;
    }

    public function addNewSubscriberEvent(SubscriptionEvent $newSubscriberEvents): self
    {
        $this->newSubscriberEvents[] = $newSubscriberEvents;

        return $this;
    }

    /**
     * @return SubscriptionEvent[]|ArrayCollection
     */
    public function getNewSubscriberEvents(): iterable
    {
        return $this->newSubscriberEvents;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function updateCreatedAt(\DateTime $date): self
    {
        $this->createdAt = $date;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function updatePrivacy(bool $public, bool $whitelistOnly): self
    {
        $this->public = $public;
        $this->whitelistOnly = $whitelistOnly;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function isWhitelistOnly(): ?bool
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
