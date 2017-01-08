<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="log", schema="subscriptions", indexes={
 *      @ORM\Index(name="author_idx", columns={"author_id"}),
 *      @ORM\Index(name="subscriber_idx", columns={"subscriber_id"}),
 *      @ORM\Index(name="date_idx", columns={"date"})
 * })
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionEventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SubscriptionEvent
{
    const ACTION_SUBSCRIBE = 'subscribe';
    const ACTION_UNSUBSCRIBE = 'unsubscribe';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User Blog author
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="newSubscriberEvents")
     * @ORM\JoinColumn(name="author_id", nullable=false)
     */
    private $author;

    /**
     * @var User Blog subscriber
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="subscriber_id", nullable=false)
     */
    private $subscriber;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=12, nullable=false)
     */
    private $action;


    /**
     * @param User $author
     * @param User $subscriber
     * @param string $action
     */
    public function __construct(User $author, User $subscriber, string $action = self::ACTION_SUBSCRIBE)
    {
        $this->author = $author;
        $this->subscriber = $subscriber;
        $this->action = $action;
    }

    /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        if (!$this->date) {
            $this->date = new \DateTime();
        }
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
