<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class SubscriptionEventRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function getLastDayEventsCount(): int
    {
        $qb = $this->createQueryBuilder('se');

        $now = new \DateTime();

        return $qb
            ->select('COUNT(se)')
            ->where('se.date > :time')
            ->setParameter('time', $now->sub(new \DateInterval('PT24H')))
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /**
     * Creates QueryBuilder object for pagination of user subscribers events
     *
     * @param User $user
     *
     * @return QueryBuilder
     */
    public function createUserLastSubscribersEventsQuery(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select(['se', 's'])
            ->join('se.subscriber', 's')
            ->where('se.author = :author')
            ->orderBy('se.date', 'desc')
            ->setParameter('author', $user->getId())
        ;
    }

    /**
     * Get last user subscriber events
     *
     * @param User $user
     * @param int $limit
     *
     * @return SubscriptionEvent[]
     */
    public function getUserLastSubscribersEvents(User $user, int $limit = 20): array
    {
        $qb = $this->createUserLastSubscribersEventsQuery($user);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get last global subscriptions QueryBuilder for pagination
     *
     * @return QueryBuilder
     */
    public function createLastSubscriptionEventsQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select(['se', 'a', 's'])
            ->innerJoin('se.author', 'a')
            ->innerJoin('se.subscriber', 's')
            ->orderBy('se.date', 'desc')
        ;
    }

    /**
     * Get last global subscription events
     *
     * @param int $limit
     *
     * @return SubscriptionEvent[]
     */
    public function getLastSubscriptionEvents(int $limit = 20): array
    {
        $qb = $this->createLastSubscriptionEventsQuery();
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return SubscriptionEvent[]
     */
    public function getLastEventsByDay(int $days = 30): array
    {
        $qb = $this->createQueryBuilder('se');

        $rows =  $qb
            ->select([
                'NEW Skobkin\Bundle\PointToolsBundle\DTO\DailyEvents(DAY(se.date), COUNT(se))',
                'DAY(se.date) as day',
            ])
            ->groupBy('day')
            ->orderBy('day', 'DESC')
            ->setMaxResults($days)
            ->getQuery()->getResult()
        ;

        $result = [];

        // Removing unnecessary element, saving DTO
        // @todo remove crutches, refactor query
        foreach ($rows as $row) {
            unset($row['day']);
            $result[] = reset($row);
        }

        return $result;
    }
}