<?php

declare(strict_types=1);

namespace App\Repository\Telegram;

use App\Entity\Telegram\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 *
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function save(Account $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAccountsCount(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()->getSingleScalarResult()
        ;
    }

    public function getLinkedAccountsCount(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.user IS NOT NULL')
            ->getQuery()->getSingleScalarResult()
        ;
    }
}
