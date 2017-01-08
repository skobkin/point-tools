<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="subscriptions", schema="subscriptions", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="subscription_unique", columns={"author_id", "subscriber_id"})}
 * )
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionRepository", readOnly=true)
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
     * @param User $author
     * @param User $subscriber
     */
    public function __construct(User $author, User $subscriber)
    {
        $this->author = $author;
        $this->subscriber = $subscriber;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getSubscriber(): User
    {
        return $this->subscriber;
    }
}
