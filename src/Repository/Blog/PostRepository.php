<?php
declare(strict_types=1);

namespace App\Repository\Blog;

use App\Entity\Blog\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPostWithComments(string $postId): ?Post
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->select(['p', 'c', 'a'])
            ->leftJoin('p.comments', 'c')
            // @todo Optimize https://ocramius.github.io/blog/doctrine-orm-optimization-hydration/
            ->leftJoin('c.author', 'a')
            ->where($qb->expr()->eq('p.id', ':post_id'))
            ->orderBy('c.number', 'asc')
            ->setParameter('post_id', $postId)
            ->getQuery()->getOneOrNullResult()
            ;
    }

    public function createPublicFeedPostsQuery(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            // @todo optimize hydration
            ->select(['p', 'pa', 'pt', 'pf'])
            ->innerJoin('p.author', 'pa')
            ->leftJoin('p.postTags', 'pt')
            ->leftJoin('p.files', 'pf')
            ->where('p.private = FALSE')
            ->andWhere('pa.public = TRUE')
            ->orderBy('p.createdAt', 'desc')
            ;
    }
}
