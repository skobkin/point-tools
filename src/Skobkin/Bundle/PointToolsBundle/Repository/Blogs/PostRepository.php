<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;

class PostRepository extends EntityRepository
{
    public function add(Post $entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    public function getPostWithComments($postId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->select(['p', 'c', 'a'])
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.author', 'a')
            ->where($qb->expr()->eq('p.id', ':post_id'))
            ->orderBy('c.number', 'asc')
            ->setParameter('post_id', $postId)
            ->getQuery()->getOneOrNullResult()
        ;
    }
}