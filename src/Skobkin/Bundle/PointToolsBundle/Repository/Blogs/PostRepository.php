<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class PostRepository extends EntityRepository
{
    public function getPostWithComments($postId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->select(['p', 'c', 'a'])
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.author', 'a')
            ->where($qb->expr()->eq('p.id', ':post_id'))
            ->setParameter('post_id', $postId)
            ->getQuery()->getOneOrNullResult()
        ;
    }
}