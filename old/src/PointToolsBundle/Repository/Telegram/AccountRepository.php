<?php

namespace src\PointToolsBundle\Repository\Telegram;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\Telegram\Account;

class AccountRepository extends EntityRepository
{
    public function add(Account $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * Returns total number of accounts
     *
     * @return int
     */
    public function getAccountsCount(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()->getSingleScalarResult()
        ;
    }

    /**
     * Returns number of accounts with linked Point.im profile
     *
     * @return int
     */
    public function getLinkedAccountsCount(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.user IS NOT NULL')
            ->getQuery()->getSingleScalarResult()
        ;
    }
}