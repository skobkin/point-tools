<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRenameEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRenameEventRepository::class, readOnly: true)]
#[ORM\Table(name: 'rename_log', schema: 'users')]
class UserRenameEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'date', type: 'datetime')]
    private \DateTime $date;

    #[ORM\Column(name: 'old_login', type: 'text')]
    private string $oldLogin;


    public function __construct(User $user, string $old)
    {
        $this->user = $user;
        $this->oldLogin = $old;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOldLogin(): string
    {
        return $this->oldLogin;
    }
}
