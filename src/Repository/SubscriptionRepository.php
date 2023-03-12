<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function save(Subscription $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

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

    /** @param User[] $subscribers */
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
    }
}
