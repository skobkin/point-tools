<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
     * @param User $user
     * @param integer $limit
     * @return SubscriptionEvent[]
     */
    public function getUserLastSubscriptionEventsById(User $user, $limit)
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException('$limit must be an integer');
        }

        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select()
            ->where('se.author = :author')
            ->orderBy('se.date', 'desc')
            ->setMaxResults($limit)
            ->setParameter('author', $user)
            ->getQuery()->getResult()
        ;
    }
}