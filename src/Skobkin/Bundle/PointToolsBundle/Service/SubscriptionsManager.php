<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Event\UserSubscribersUpdatedEvent;
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionEventRepository;
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SubscriptionsManager
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepo;

    /**
     * @var SubscriptionEventRepository
     */
    private $subscriptionRecordRepo;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        SubscriptionRepository $subscriptionRepo,
        SubscriptionEventRepository $subscriptionRecordRepo
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->subscriptionRecordRepo = $subscriptionRecordRepo;
    }

    /**
     * @param User $user
     * @param User[] $newSubscribersList
     */
    public function updateUserSubscribers(User $user, $newSubscribersList = []): void
    {
        $tmpOldSubscribers = $user->getSubscribers();

        $oldSubscribersList = [];

        foreach ($tmpOldSubscribers as $subscription) {
            $oldSubscribersList[] = $subscription->getSubscriber();
        }

        $this->logger->debug('Counting user subscribers diff', ['user_id' => $user->getId()]);

        $subscribedList = $this->getUsersListsDiff($newSubscribersList, $oldSubscribersList);
        $unsubscribedList = $this->getUsersListsDiff($oldSubscribersList, $newSubscribersList);

        $this->logger->debug(sprintf('User has %d subscribed and %d unsubscribed users', count($subscribedList), count($unsubscribedList)));

        $this->processSubscribedUsers($user, $subscribedList);
        $this->processUnsubscribedUsers($user, $unsubscribedList);

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
    private function processSubscribedUsers(User $user, array $subscribers): void
    {
        $this->logger->debug('Processing subscribed users');

        foreach ($subscribers as $subscriber) {
            $subscription = new Subscription($user, $subscriber);

            $user->addSubscriber($subscription);
            $this->subscriptionRepo->add($subscription);

            $logEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_SUBSCRIBE);
            $this->subscriptionRecordRepo->add($logEvent);

            $user->addNewSubscriberEvent($logEvent);
        }
    }

    /**
     * @param User $user
     * @param User[] $subscribers
     */
    private function processUnsubscribedUsers(User $user, array $subscribers): void
    {
        $this->logger->debug('Processing unsubscribed users');

        foreach ($subscribers as $subscriber) {
            $logEvent = new SubscriptionEvent($user, $subscriber, SubscriptionEvent::ACTION_UNSUBSCRIBE);
            $this->subscriptionRecordRepo->add($logEvent);

            $user->addNewSubscriberEvent($logEvent);
        }

        // Removing users from database
        // @todo Refactor to collection usage (after dealing with orphanRemoval bug)
        $this->subscriptionRepo->removeSubscribers($user, $subscribers);
    }

    /**
     * @param User $user
     * @param User[] $subscribed
     * @param User[] $unsubscribed
     */
    private function dispatchSubscribersUpdatedEvent(User $user, array $subscribed, array $unsubscribed): void
    {
        if (0 !== count($subscribed) || 0 !== count($unsubscribed)) {
            // Dispatching event
            $subscribersUpdatedEvent = new UserSubscribersUpdatedEvent($user, $subscribed, $unsubscribed);
            $this->eventDispatcher->dispatch(UserSubscribersUpdatedEvent::NAME, $subscribersUpdatedEvent);
        }
    }
}