<?php

namespace src\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use src\PointToolsBundle\Entity\User;

/**
 * @ORM\Table(name="rename_log", schema="users", indexes={
 *     @ORM\Index(name="idx_rename_log_date", columns={"date"}),
 *     @ORM\Index(name="idx_rename_log_old_login", columns={"old_login"})
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\UserRenameEventRepository", readOnly=true)
 */
class UserRenameEvent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Skobkin\Bundle\PointToolsBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="old_login", type="text")
     */
    private $oldLogin;


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
