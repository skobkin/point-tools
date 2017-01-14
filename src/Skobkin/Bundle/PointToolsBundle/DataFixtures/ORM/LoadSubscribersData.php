<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

/**
 * Load user subscriptions
 */
class LoadSubscribersData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $om)
    {
        /** @var User[] $users */
        $users = [
            99999 => $this->getReference('test_user_99999'),
            99998 => $this->getReference('test_user_99998'),
            99997 => $this->getReference('test_user_99997'),
            99996 => $this->getReference('test_user_99996'),
            99995 => $this->getReference('test_user_99995'),
        ];

        $subscriptions = [
            99999 => [99998, 99997, 99996, 99995],
            99998 => [99999, 99997],
            99997 => [99999],
        ];

        foreach ($users as $key => $user) {
            if (array_key_exists($key, $subscriptions)) {
                foreach ($subscriptions[$key] as $userId) {
                    $subscriber = $users[$userId];
                    $subscription = new Subscription($user, $subscriber);
                    $subscriptionEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_SUBSCRIBE);
                    $om->persist($subscription);
                    $om->persist($subscriptionEvent);
                    $user->addSubscriber($subscription);
                }
            }
        }

        $om->flush();
    }

    public function getOrder(): int
    {
        return 4;
    }
}