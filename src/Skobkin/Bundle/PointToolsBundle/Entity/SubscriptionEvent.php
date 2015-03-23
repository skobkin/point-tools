<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubscriptionEvent
 *
 * @ORM\Table(name="subscriptions_events")
 * @ORM\Entity
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
     * @var User Blog subscriber
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="newSubscriptionEvents")
     * @ORM\JoinColumn(name="subscriber_id", nullable=false)
     */
    private $subscriber;
    
    /**
     * @var User Blog author
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="newSubscriberEvents")
     * @ORM\JoinColumn(name="author_id", nullable=false)
     */
    private $author;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $dateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=12, nullable=false)
     */
    private $action;

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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return SubscriptionEvent
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set subscriber
     *
     * @param User $subscriber
     * @return SubscriptionEvent
     */
    public function setSubscriber(User $subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
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
     * Set author
     *
     * @param User $author
     * @return SubscriptionEvent
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
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
     * Set action
     *
     * @param string $action
     * @return SubscriptionEvent
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
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
