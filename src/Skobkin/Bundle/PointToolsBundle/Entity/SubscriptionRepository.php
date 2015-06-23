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
        if (!is_int($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $qb = $this->createQueryBuilder('s');
        return $qb
            ->select('COUNT(s)')
            ->innerJoin('s.author', 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /**
     * @return TopUserDTO[]
     */
    public function getTopUsers()
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->select(['COUNT(s.subscriber) as cnt', 'NEW SkobkinPointToolsBundle:TopUserDTO(a.login, COUNT(s.subscriber))'])
            ->innerJoin('s.author', 'a')
            ->orderBy('cnt', 'desc')
            ->groupBy('a.id')
            ->setMaxResults(30)
            ->getQuery()->getResult()
        ;
    }
}