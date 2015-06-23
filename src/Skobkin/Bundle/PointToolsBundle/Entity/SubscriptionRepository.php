<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SubscriptionRepository extends EntityRepository
{
    /**
     * @param integer $id
     * @return integer
     */
    public function getUserSubscribersCountById($id)
    {
        $qb = $this->createQueryBuilder('s');
        return $qb
            ->select('COUNT(s)')
            ->innerJoin('s.author', 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getSingleScalarResult()
        ;
    }
}