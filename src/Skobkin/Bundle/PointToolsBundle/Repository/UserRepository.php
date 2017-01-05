<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class UserRepository extends EntityRepository
{
    /**
     * Case-insensitive user search
     *
     * @param string $login
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByLogin(string $login)
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
     * Case insensitive user LIKE %login% search
     *
     * @param string $login
     * @param int $limit
     *
     * @return User[]
     */
    public function findUsersLikeLogin(string $login, int $limit = 10)
    {
        if (empty($login)) {
            return [];
        }

        $qb = $this->createQueryBuilder('u');

        return $qb
            ->where('LOWER(u.login) LIKE LOWER(:login)')
            ->orderBy('u.login', 'ASC')
            ->setMaxResults($limit)
            ->setParameter('login', '%'.$login.'%')
            ->getQuery()
            ->getResult()
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
    public function findUserSubscribersById(int $id)
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

    /**
     * Returns top users by subscribers count
     *
     * @param int $limit
     *
     * @return TopUserDTO[]
     */
    public function getTopUsers(int $limit = 30)
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException('$limit must be an integer');
        }

        $qb = $this->getEntityManager()->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('s');

        $rows = $qb
            ->select([
                'NEW Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO(a.login, COUNT(s.subscriber))',
                'COUNT(s.subscriber) as subscribers_count'
            ])
            ->innerJoin('s.author', 'a')
            ->orderBy('subscribers_count', 'desc')
            ->groupBy('a.id')
            ->setMaxResults($limit)
            ->getQuery()->getResult()
        ;

        $result = [];

        // Removing subscribers_count element, saving TopUserDTO
        // @todo remove crutches, refactor query
        foreach ($rows as $row) {
            unset($row['subscribers_count']);
            $result[] = reset($row);
        }

        return $result;
    }
}