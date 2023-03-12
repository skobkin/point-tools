<?php

declare(strict_types=1);

namespace App\Entity\Telegram;

use App\Entity\User;
use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'telegram_accounts', schema: 'users')]
#[ORM\Index(columns: ['subscriber_notification'], name: 'subscriber_notification_idx', options: [
    'where' => 'subscriber_notification = TRUE',
])]
#[ORM\Index(columns: ['rename_notification'], name: 'rename_notification_idx', options: [
    'where' => 'rename_notification = TRUE',
])]
class Account
{
    #[ORM\Id]
    #[ORM\Column(name: 'account_id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: '', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\Column(name: 'linked_at', type: 'datetime', nullable: true)]
    private ?\DateTime $linkedAt;

    #[ORM\Column(name: 'first_name', type: 'text')]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: 'text', nullable: true)]
    private ?string $lastName;

    #[ORM\Column(name: 'username', type: 'text', nullable: true)]
    private ?string $username;

    #[ORM\Column(name: 'private_chat_id', type: 'bigint', nullable: true)]
    private ?int $chatId;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: true, onDelete: 'CASCADE')]
    private ?User $user;

    #[ORM\Column(name: 'subscriber_notification', type: 'boolean')]
    private bool $subscriberNotification = false;

    #[ORM\Column(name: 'rename_notification', type: 'boolean')]
    private bool $renameNotification = false;


    public function __construct(int $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTime();
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

    public function setUser(User $user): self
    {
        if ($this->user !== $user) {
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
