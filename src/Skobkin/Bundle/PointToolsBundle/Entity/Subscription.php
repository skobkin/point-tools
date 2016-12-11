<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table(name="subscriptions", schema="subscriptions", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="subscription_unique", columns={"author_id", "subscriber_id"})}
 * )
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscribers")
     * @ORM\JoinColumn(name="author_id")
     */
    private $author;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="subscriber_id")
     */
    private $subscriber;


    /**
     * Subscription constructor.
     *
     * @param User $author
     * @param User $subscriber
     */
    public function __construct(User $author = null, User $subscriber = null)
    {
        $this->author = $author;
        $this->subscriber = $subscriber;
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
     * Get subscriber
     *
     * @return User
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }
}
