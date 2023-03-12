<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserRenameEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRenameEvent>
 *
 * @method UserRenameEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRenameEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRenameEvent[]    findAll()
 * @method UserRenameEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRenameEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRenameEvent::class);
    }

    public function save(UserRenameEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
