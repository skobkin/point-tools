<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users", schema="users")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @var int
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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var ArrayCollection|Subscription[]
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $subscribers;

    /**
     * @var ArrayCollection|Subscription[]
     *
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="subscriber", fetch="EXTRA_LAZY")
     */
    private $subscriptions;

    /**
     * @var ArrayCollection|SubscriptionEvent[]
     * @ORM\OneToMany(targetEntity="SubscriptionEvent", mappedBy="author", fetch="EXTRA_LAZY")
     */
    private $newSubscriberEvents;


    /**
     * @param int $id
     * @param string $login
     * @param string $name
     */
    public function __construct(int $id, string $login = null, string $name = null)
    {
        $this->id = $id;
        $this->login = $login;
        $this->name = $name;

        $this->subscribers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->newSubscriberEvents = new ArrayCollection();
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
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $login
     * @return User
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addSubscriber(Subscription $subscribers): self
    {
        $this->subscribers[] = $subscribers;

        return $this;
    }

    public function removeSubscriber(Subscription $subscribers)
    {
        $this->subscribers->removeElement($subscribers);
    }

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function addNewSubscriberEvent(SubscriptionEvent $newSubscriberEvents): self
    {
        $this->newSubscriberEvents[] = $newSubscriberEvents;

        return $this;
    }

    /**
     * @return SubscriptionEvent[]|ArrayCollection
     */
    public function getNewSubscriberEvents()
    {
        return $this->newSubscriberEvents;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
