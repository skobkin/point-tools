<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class SubscriptionEventRepository extends EntityRepository
{
    /**
     * @return integer
     */
    public function getLastDayEventsCount()
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
    public function createUserLastSubscribersEventsQuery(User $user)
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
     * Get last $limit subscriptions
     *
     * @param integer $limit
     * @return SubscriptionEvent[]
     */
    public function getLastSubscriptionEvents($limit)
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException('$limit must be an integer');
        }

        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select()
            ->orderBy('se.date', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
                ->setFetchMode('SkobkinPointToolsBundle:SubscriptionEvent', 'author', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('SkobkinPointToolsBundle:SubscriptionEvent', 'subscriber', ClassMetadata::FETCH_EAGER)
            ->getResult()
        ;
    }
}