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
        $tmpOldSubscribers = $user->getSubscribers();

        $oldSubscribersList = [];

        foreach ($tmpOldSubscribers as $subscription) {
            $oldSubscribersList[] = $subscription->getSubscriber();
        }

        $subscribedList = $this->getUsersListsDiff($newSubscribersList, $oldSubscribersList);
        $unsubscribedList = $this->getUsersListsDiff($oldSubscribersList, $newSubscribersList);

        $this->processSubscribedUsers($user, $subscribedList);
        $this->processUnsubscribedUsers($user, $unsubscribedList);

        // Removing users from database
        // @todo Maybe remove via ORM
        $this->em->getRepository('SkobkinPointToolsBundle:Subscription')->removeSubscribers($user, $unsubscribedList);

        $this->dispatchSubscribersUpdatedEvent($user, $subscribedList, $unsubscribedList);
    }

    /**
     * Compares $list1 against $list2 and returns the values in $list1 that are not present in $list2.
     *
     * @param User[] $list1
     * @param User[] $list2
     *
     * @return User[]
     */
    public function getUsersListsDiff(array $list1 = [], array $list2 = []): array
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

    /**
     * @param User $user
     * @param User[] $subscribers
     */
    private function processSubscribedUsers(User $user, array $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $subscription = new Subscription($user, $subscriber);

            $user->addSubscriber($subscription);
            $this->em->persist($subscription);

            $logEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_SUBSCRIBE);
            $this->em->persist($logEvent);

            $user->addNewSubscriberEvent($logEvent);
        }
    }

    /**
     * @param User $user
     * @param User[] $subscribers
     */
    private function processUnsubscribedUsers(User $user, array $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $logEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_UNSUBSCRIBE);
            $this->em->persist($logEvent);

            $user->addNewSubscriberEvent($logEvent);
        }
    }

    /**
     * @param User $user
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    private function dispatchSubscribersUpdatedEvent(User $user, array $subscribed, array $unsubscribed)
    {
        if (0 !== count($subscribed) || 0 !== count($unsubscribed)) {
            // Dispatching event
            $subscribersUpdatedEvent = new UserSubscribersUpdatedEvent($user, $subscribed, $unsubscribed);
            $this->eventDispatcher->dispatch(UserSubscribersUpdatedEvent::NAME, $subscribersUpdatedEvent);
        }
    }
}