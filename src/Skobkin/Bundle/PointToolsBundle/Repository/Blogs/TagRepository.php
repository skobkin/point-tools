<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;

class TagRepository extends EntityRepository
{
    /**
     * @param $text
     * @return Tag|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByLowerText($text)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where($qb->expr()->eq(
                $qb->expr()->lower('t.text'),
                $qb->expr()->lower(':text')
            ))
            ->setParameter('text', $text)
            ->getQuery()->getOneOrNullResult()
        ;
    }
}