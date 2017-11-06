<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;

class TagRepository extends EntityRepository
{
    public function add(Tag $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function findOneByLowerText(string $text): ?Tag
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->where('LOWER(t.text) = :text')
            ->setParameter('text', mb_strtolower($text))
            ->getQuery()->getOneOrNullResult()
        ;
    }
}