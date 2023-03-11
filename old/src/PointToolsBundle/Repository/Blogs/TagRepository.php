<?php

namespace src\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\Blogs\Tag;
use function Skobkin\Bundle\PointToolsBundle\Repository\Blogs\mb_strtolower;

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