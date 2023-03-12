<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SubscriptionEvent;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriptionEvent>
 *
 * @method SubscriptionEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionEvent[]    findAll()
 * @method SubscriptionEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionEvent::class);
    }

    public function save(SubscriptionEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastDayEventsCount(): int
    {
        $qb = $this->createQueryBuilder('se');

        $from = (new \DateTime())->sub(new \DateInterval('PT24H'));

        return $qb
            ->select('COUNT(se)')
            ->where('se.date > :from_time')
            ->setParameter('from_time', $from)
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /** Creates QueryBuilder object for pagination of user subscribers events */
    public function createUserLastSubscribersEventsQuery(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select(['se', 's'])
            ->join('se.subscriber', 's')
            ->where('se.author = :author')
            ->orderBy('se.date', 'desc')
            ->setParameter('author', $user->getId())
        ;
    }

    /**
     * Get last user subscriber events
     *
     * @return SubscriptionEvent[]
     */
    public function getUserLastSubscribersEvents(User $user, int $limit = 20): array
    {
        $qb = $this->createUserLastSubscribersEventsQuery($user);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /** Get last global subscriptions QueryBuilder for pagination */
    public function createLastSubscriptionEventsQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('se');

        return $qb
            ->select(['se', 'a', 's'])
            ->innerJoin('se.author', 'a')
            ->innerJoin('se.subscriber', 's')
            ->orderBy('se.date', 'desc')
        ;
    }

    /**
     * Get last global subscription events
     *
     * @return SubscriptionEvent[]
     */
    public function getLastSubscriptionEvents(int $limit = 20): array
    {
        $qb = $this->createLastSubscriptionEventsQuery();
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /** @return SubscriptionEvent[] */
    public function getLastEventsByDay(int $days = 30): array
    {
        $qb = $this->createQueryBuilder('se');

        $rows =  $qb
            ->select([
                'NEW Skobkin\Bundle\PointToolsBundle\DTO\DailyEvents(DAY(se.date), COUNT(se))',
                'DAY(se.date) as day',
            ])
            ->groupBy('day')
            ->orderBy('day', 'DESC')
            ->setMaxResults($days)
            ->getQuery()->getResult()
        ;

        $result = [];

        // Removing unnecessary element, saving DTO
        // @todo remove crutches, refactor query
        foreach ($rows as $row) {
            unset($row['day']);
            $result[] = reset($row);
        }

        $result = array_reverse($result);

        return $result;
    }
}
