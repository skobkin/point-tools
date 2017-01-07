<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Event\UserSubscribersUpdatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SubscriptionsManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param User $user
     * @param User[] $newSubscribersList
     */
    public function updateUserSubscribers(User $user, $newSubscribersList = [])
    {
        /** @var Subscription[] $tmpOldSubscribers */
        $tmpOldSubscribers = $user->getSubscribers();

        $oldSubscribersList = [];

        foreach ($tmpOldSubscribers as $subscription) {
            $oldSubscribersList[] = $subscription->getSubscriber();
        }

        // @todo remove
        $isFirstTime = false;

        // Preventing to add garbage subscriptions for first processing
        // @todo improve algorithm
        if ((count($oldSubscribersList) === 0) && (count($newSubscribersList) > 1)) {
            $isFirstTime = true;
        }

        unset($tmpOldSubscribers);

        $subscribedList = $this->getUsersListsDiff($newSubscribersList, $oldSubscribersList);
        $unsubscribedList = $this->getUsersListsDiff($oldSubscribersList, $newSubscribersList);

        /** @var User $subscribedUser */
        foreach ($subscribedList as $subscribedUser) {
            $subscription = new Subscription($user, $subscribedUser);

            $user->addSubscriber($subscription);
            $this->em->persist($subscription);

            // If it's not first processing
            if (!$isFirstTime) {
                $logEvent = new SubscriptionEvent($user, $subscribedUser, SubscriptionEvent::ACTION_SUBSCRIBE);
                $this->em->persist($logEvent);

                $user->addNewSubscriberEvent($logEvent);
            }
        }

        /** @var User $unsubscribedUser */
        foreach ($unsubscribedList as $unsubscribedUser) {
            $logEvent = new SubscriptionEvent($user, $unsubscribedUser, SubscriptionEvent::ACTION_UNSUBSCRIBE);
            $this->em->persist($logEvent);

            $user->addNewSubscriberEvent($logEvent);
        }

        // Removing users from database
        $this->em->getRepository('SkobkinPointToolsBundle:Subscription')->removeSubscribers($user, $unsubscribedList);

        if (0 !== count($subscribedList) || 0 !== count($unsubscribedList)) {
            // Dispatching event
            $subscribersUpdatedEvent = new UserSubscribersUpdatedEvent($user, $subscribedList, $unsubscribedList);
            $this->eventDispatcher->dispatch(UserSubscribersUpdatedEvent::NAME, $subscribersUpdatedEvent);
        }
    }

    /**
     * Compares $list1 against $list2 and returns the values in $list1 that are not present in $list2.
     *
     * @param User[] $list1
     * @param User[] $list2
     *
     * @return User[] Diff
     */
    public function getUsersListsDiff(array $list1 = [], array $list2 = [])
    {
        $hash1 = [];
        $hash2 = [];

        foreach ($list1 as $user) {
            $hash1[$user->getId()] = $user;
        }
        foreach ($list2 as $user) {
            $hash2[$user->getId()] = $user;
        }

        return array_diff_key($hash1, $hash2);
    }
}