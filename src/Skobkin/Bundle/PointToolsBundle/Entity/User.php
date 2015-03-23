<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users", indexes={
 *      @ORM\Index(name="idx_name", columns={"name"})
 * })
 * @ORM\Entity
 */
class User
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return ArrayCollection 
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
}
