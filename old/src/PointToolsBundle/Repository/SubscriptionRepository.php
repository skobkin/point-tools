<?php

namespace src\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\Subscription;
use src\PointToolsBundle\Entity\User;

class SubscriptionRepository extends EntityRepository
{
    public function add(Subscription $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function getUserSubscribersCountById(int $id): int
    {
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
    public function removeSubscribers(User $user, array $subscribers): void
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