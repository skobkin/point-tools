<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table(name="subscriptions.subscriptions", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="subscription_unique", columns={"author_id", "subscriber_id"})}
 * )
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionRepository")
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
     * Set author
     *
     * @param User $author
     * @return Subscription
     */
    public function setAuthor(User $author = null)
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
     * Set subscriber
     *
     * @param User $subscriber
     * @return Subscription
     */
    public function setSubscriber(User $subscriber = null)
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
}
