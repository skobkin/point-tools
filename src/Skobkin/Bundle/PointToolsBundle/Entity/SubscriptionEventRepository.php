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

        $eventsCount = $qb
            ->select('COUNT(se)')
            ->where('se.date > :time')
            ->setParameter('time', $now->sub(new \DateInterval('PT24H')))
            ->getQuery()->getSingleScalarResult()
        ;
    }
}