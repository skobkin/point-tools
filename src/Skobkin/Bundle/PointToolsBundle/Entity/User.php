<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users.users", schema="users")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @var integer
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
     * @var string
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="author")
     */
    private $subscribers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="subscriber")
     */
    private $subscriptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SubscriptionEvent", mappedBy="subscriber")
     */
    private $newSubscriptionEvents;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="SubscriptionEvent", mappedBy="author")
     */
    private $newSubscriberEvents;


    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->newSubscriberEvents = new ArrayCollection();
        $this->newSubscriptionEvents = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
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
     * Set id of user (for API services only)
     *
     * @param integer $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add subscribers
     *
     * @param Subscription $subscribers
     * @return User
     */
    public function addSubscriber(Subscription $subscribers)
    {
        $this->subscribers[] = $subscribers;

        return $this;
    }

    /**
     * Remove subscribers
     *
     * @param Subscription $subscribers
     */
    public function removeSubscriber(Subscription $subscribers)
    {
        $this->subscribers->removeElement($subscribers);
    }

    /**
     * Get subscribers
     *
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * Add subscriptions
     *
     * @param Subscription $subscriptions
     * @return User
     */
    public function addSubscription(Subscription $subscriptions)
    {
        $this->subscriptions[] = $subscriptions;

        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @param Subscription $subscriptions
     */
    public function removeSubscription(Subscription $subscriptions)
    {
        $this->subscriptions->removeElement($subscriptions);
    }

    /**
     * Get subscriptions
     *
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Add newSubscriptionEvents
     *
     * @param SubscriptionEvent $newSubscriptionEvents
     * @return User
     */
    public function addNewSubscriptionEvent(SubscriptionEvent $newSubscriptionEvents)
    {
        $this->newSubscriptionEvents[] = $newSubscriptionEvents;

        return $this;
    }

    /**
     * Remove newSubscriptionEvents
     *
     * @param SubscriptionEvent $newSubscriptionEvents
     */
    public function removeNewSubscriptionEvent(SubscriptionEvent $newSubscriptionEvents)
    {
        $this->newSubscriptionEvents->removeElement($newSubscriptionEvents);
    }

    /**
     * Get newSubscriptionEvents
     *
     * @return ArrayCollection
     */
    public function getNewSubscriptionEvents()
    {
        return $this->newSubscriptionEvents;
    }

    /**
     * Add newSubscriberEvents
     *
     * @param SubscriptionEvent $newSubscriberEvents
     * @return User
     */
    public function addNewSubscriberEvent(SubscriptionEvent $newSubscriberEvents)
    {
        $this->newSubscriberEvents[] = $newSubscriberEvents;

        return $this;
    }

    /**
     * Remove newSubscriberEvents
     *
     * @param SubscriptionEvent $newSubscriberEvents
     */
    public function removeNewSubscriberEvent(SubscriptionEvent $newSubscriberEvents)
    {
        $this->newSubscriberEvents->removeElement($newSubscriberEvents);
    }

    /**
     * Get newSubscriberEvents
     *
     * @return ArrayCollection
     */
    public function getNewSubscriberEvents()
    {
        return $this->newSubscriberEvents;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
