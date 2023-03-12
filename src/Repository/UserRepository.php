<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveUserWithSubscribers(int $id): ?User
    {
        $qb = $this->createQueryBuilder('u');

        // @todo May be optimize hydration procedure
        return $qb
            ->select(['u', 's', 'us'])
            ->innerJoin('u.subscribers', 's')
            ->innerJoin('s.subscriber', 'us')
            ->where('u.id = :user_id')
            ->andWhere('u.removed = FALSE')
            ->setParameter('user_id', $id)
            ->getQuery()->getOneOrNullResult()
        ;
    }

    /** Case-insensitive user search */
    public function findUserByLogin(string $login): ?User
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
     * @return User[]
     */
    public function findUsersLikeLogin(string $login, int $limit = 10): array
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

    public function getUsersCount(): int
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('COUNT(u)')->getQuery()->getSingleScalarResult();
    }

    /** @return User[] */
    public function findUserSubscribersById(int $id): array
    {
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
     * @return TopUserDTO[]
     */
    public function getTopUsers(int $limit = 30): array
    {
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
