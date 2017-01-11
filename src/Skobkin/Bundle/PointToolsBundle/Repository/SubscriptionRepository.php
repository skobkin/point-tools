<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class SubscriptionRepository extends EntityRepository
{
    public function add(Subscription $entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function getUserSubscribersCountById($id): int
    {
        if (!is_int($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $qb = $this->createQueryBuilder('s');
        return $qb
            ->select('COUNT(s.subscriber)')
            ->innerJoin('s.author', 'sa')
            ->where('sa.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /**
     * @param User $user
     * @param User[] $subscribers
     */
    public function removeSubscribers(User $user, array $subscribers)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->delete()
            ->where('s.author = :author')
            ->andWhere('s.subscriber IN (:subscribers)')
            ->setParameter('author', $user->getId())
            ->setParameter('subscribers', $subscribers)
            ->getQuery()->execute();
        ;
    }
}