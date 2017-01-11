<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Telegram;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;

class AccountRepository extends EntityRepository
{
    public function add(Account $entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @todo remove if not used
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return Account[]
     */
    public function findLinkedAccountsBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('a');

        $i = 0;
        foreach ($criteria as $property => $value) {
            $qb
                ->andWhere('a.'.$property.' = :criteria_'.$i)
                ->setParameter('criteria_'.$i, $value)
            ;
        }

        if (null !== $orderBy) {
            foreach ($orderBy as $property => $order) {
                $qb->addOrderBy($property, $order);
            }
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
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