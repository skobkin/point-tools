<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Case-insensitive user search
     *
     * @param string $login
     * @return User[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByLogin($login)
    {
        $qb = $this->createQueryBuilder('u');

        return $qb
            ->select('u')
            ->where('LOWER(u.login) = LOWER(:login)')
            ->setMaxResults(1)
            ->setParameter('login', $login)
            ->getQuery()->getOneOrNullResult()
        ;
    }

    /**
     * @return integer
     */
    public function getUsersCount()
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('COUNT(u)')->getQuery()->getSingleScalarResult();
    }

    /**
     * @param integer $id
     * @return User[]
     */
    public function findUserSubscribersById($id)
    {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $qb = $this->createQueryBuilder('u');

        return $qb
            ->select('u')
            ->innerJoin('u.subscriptions', 's')
            ->where('s.author = :author')
            ->orderBy('u.login', 'asc')
            ->setParameter('author', $id)
            ->getQuery()->getResult()
        ;
    }
}