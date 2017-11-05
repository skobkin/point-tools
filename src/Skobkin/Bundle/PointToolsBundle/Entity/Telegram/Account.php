<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Telegram;

use Doctrine\ORM\Mapping as ORM;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

/**
 * @ORM\Table(name="telegram_accounts", schema="users", indexes={
 *      @ORM\Index(name="subscriber_notification_idx", columns={"subscriber_notification"}, options={"where": "subscriber_notification = TRUE"}),
 *      @ORM\Index(name="rename_notification_idx", columns={"rename_notification"}, options={"where": "rename_notification = TRUE"}),
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Telegram\AccountRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Account
{
    /**
     * Telegram user ID
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="account_id", type="integer")
     */
    private $id;

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
     * @var \DateTime
     *
     * @ORM\Column(name="linked_at", type="datetime", nullable=true)
     */
    private $linkedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="text")
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="text", nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="text", nullable=true)
     */
    private $username;

    /**
     * ID of private chat with user
     *
     * @var int
     *
     * @ORM\Column(name="private_chat_id", type="bigint", nullable=true)
     */
    private $chatId;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * Notifications about new subscribers
     *
     * @var bool
     *
     * @ORM\Column(name="subscriber_notification", type="boolean")
     */
    private $subscriberNotification = false;

    /**
     * Notifications about user renaming
     *
     * @var bool
     *
     * @ORM\Column(name="rename_notification", type="boolean")
     */
    private $renameNotification = false;


    public function __construct(int $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getLinkedAt(): \DateTime
    {
        return $this->linkedAt;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function updateFromMessageData(string $firstName, ?string $lastName, ?string $username, int $chatId): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->chatId = $chatId;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): Account
    {
        if (!$this->user && $user) {
            $this->linkedAt = new \DateTime();
        }

        $this->user = $user;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->user->getId();
    }

    public function disableNotifications(): self
    {
        $this->subscriberNotification = false;
        $this->renameNotification = false;

        return $this;
    }

    public function setSubscriberNotification(bool $subscriberNotification): self
    {
        $this->subscriberNotification = $subscriberNotification;

        return $this;
    }

    public function toggleSubscriberNotification(): self
    {
        $this->subscriberNotification = !$this->subscriberNotification;

        return $this;
    }

    public function isSubscriberNotification(): bool
    {
        return $this->subscriberNotification;
    }

    public function setRenameNotification(bool $renameNotification): self
    {
        $this->renameNotification = $renameNotification;

        return $this;
    }

    public function toggleRenameNotification(): self
    {
        $this->renameNotification = !$this->renameNotification;

        return $this;
    }

    public function isRenameNotification(): bool
    {
        return $this->renameNotification;
    }
}
