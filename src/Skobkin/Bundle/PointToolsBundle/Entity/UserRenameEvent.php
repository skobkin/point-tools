<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRenameEvent
 *
 * @ORM\Table(name="users.rename_log", schema="users", indexes={
 *     @ORM\Index(name="idx_rename_log_date", columns={"date"}),
 *     @ORM\Index(name="idx_rename_log_old_login", columns={"old_login"})
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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

    /**
     * @var string
     *
     * @ORM\Column(name="new_login", type="text")
     */
    private $newLogin;


    public function __construct(User $user, $old, $new)
    {
        $this->user = $user;
        $this->oldLogin = $old;
        $this->newLogin = $new;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserRenameEvent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set oldLogin
     *
     * @param string $oldLogin
     * @return UserRenameEvent
     */
    public function setOldLogin($oldLogin)
    {
        $this->oldLogin = $oldLogin;

        return $this;
    }

    /**
     * Get oldLogin
     *
     * @return string 
     */
    public function getOldLogin()
    {
        return $this->oldLogin;
    }

    /**
     * Set newLogin
     *
     * @param string $newLogin
     * @return UserRenameEvent
     */
    public function setNewLogin($newLogin)
    {
        $this->newLogin = $newLogin;

        return $this;
    }

    /**
     * Get newLogin
     *
     * @return string 
     */
    public function getNewLogin()
    {
        return $this->newLogin;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserRenameEvent
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
