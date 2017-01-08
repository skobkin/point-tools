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
            $this->getReference('test_user_99999'),
            $this->getReference('test_user_99998'),
            $this->getReference('test_user_99997'),
            $this->getReference('test_user_99996'),
            $this->getReference('test_user_99995'),
        ];

        foreach ($users as $key => $user) {
            // At least 2 subscribers for first user in the list
            if (0 === $key) {
                $minimum = 2;
            } else {
                $minimum = random_int(0, count($users));
            }

            foreach ($this->getRandomSubscribers($users, $minimum) as $subscriber) {
                $subscription = new Subscription($user, $subscriber);
                $subscriptionEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_SUBSCRIBE);
                $om->persist($subscription);
                $om->persist($subscriptionEvent);
                $user->addSubscriber($subscription);
            }
        }

        $om->flush();
    }

    public function getOrder()
    {
        return 4;
    }

    /**
     * Returns array with random users from given users array
     *
     * @param User[] $users
     * @param int $min
     *
     * @return User[]
     */
    private function getRandomSubscribers($users, $min = 0)
    {
        if (0 === $number = mt_rand($min, count($users))) {
            return [];
        }

        $keys = array_rand($users, $number);

        // If array_rand was called with $number = 1
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $result = [];

        foreach ($keys as $key) {
            $result[] = $users[$key];
        }

        return $result;
    }
}