<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubscriptionEvent
 *
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
     * @var integer
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="newSubscriptionEvents")
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
    public function __construct(User $author = null, User $subscriber = null, $action = self::ACTION_SUBSCRIBE)
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
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get subscriber
     *
     * @return User 
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Get author
     *
     * @return User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}
