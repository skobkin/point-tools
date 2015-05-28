<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class SubscriptionsManager
{
    /**
     * @var EntityManager
     */
    protected $em;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param User $user
     * @param User[]|array $newSubscribersList
     */
    public function updateUserSubscribers(User $user, $newSubscribersList = [])
    {
        /** @var Subscription[] $tmpOldSubscribers */
        $tmpOldSubscribers = $user->getSubscribers();

        $oldSubscribersList = [];

        foreach ($tmpOldSubscribers as $subscription) {
            $oldSubscribersList[] = $subscription->getSubscriber();
        }

        unset($tmpOldSubscribers);

        $subscribedList = $this->getUsersListsDiff($newSubscribersList, $oldSubscribersList);
        $unsubscribedList = $this->getUsersListsDiff($oldSubscribersList, $newSubscribersList);

        /** @var User $subscribedUser */
        foreach ($subscribedList as $subscribedUser) {
            $subscription = new Subscription();
            $subscription
                ->setAuthor($user)
                ->setSubscriber($subscribedUser)
            ;

            $user->addSubscriber($subscription);

            $logEvent = new SubscriptionEvent();
            $logEvent
                ->setSubscriber($subscribedUser)
                ->setAuthor($user)
                ->setAction(SubscriptionEvent::ACTION_SUBSCRIBE)
            ;

            $user->addNewSubscriberEvent($logEvent);

            $this->em->persist($subscription);
            $this->em->persist($logEvent);
        }

        unset($subscribedList);

        /** @var QueryBuilder $unsubscribedQuery */
        $unsubscribedQuery = $this->em->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('s');
        $unsubscribedQuery
            ->delete()
            ->where('s.author = :author')
            ->andWhere('s.subscriber IN (:subscribers)')
        ;


        /** @var User $unsubscribedUser */
        foreach ($unsubscribedList as $unsubscribedUser) {
            $logEvent = new SubscriptionEvent();
            $logEvent
                ->setSubscriber($unsubscribedUser)
                ->setAction($user)
                ->setAction(SubscriptionEvent::ACTION_UNSUBSCRIBE)
            ;

            $user->addNewSubscriberEvent($logEvent);

            $this->em->persist($logEvent);
        }

        $unsubscribedQuery
            ->setParameter('author', $user->getId())
            ->setParameter('subscribers', $unsubscribedList)
            ->getQuery()->execute();
        ;

        unset($unsubscribedList);

        $this->em->flush();
    }

    /**
     * Compares $list1 against $list2 and returns the values in $list1 that are not present in $list2.
     *
     * @param User[] $list1
     * @param User[] $list2
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