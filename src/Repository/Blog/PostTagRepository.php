<?php
declare(strict_types=1);

namespace App\Repository\Blog;

use App\Entity\Blog\PostTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostTag>
 *
 * @method PostTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostTag[]    findAll()
 * @method PostTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostTag::class);
    }

    public function save(PostTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
