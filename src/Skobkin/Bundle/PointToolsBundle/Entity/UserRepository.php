<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @return integer
     */
    public function getUsersCount()
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('COUNT(u)')->getQuery()->getSingleScalarResult();
    }
}