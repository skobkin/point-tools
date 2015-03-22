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


    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
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
}
