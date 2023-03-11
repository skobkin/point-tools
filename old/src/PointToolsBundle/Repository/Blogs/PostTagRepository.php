<?php

namespace src\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\Blogs\PostTag;

class PostTagRepository extends EntityRepository
{
    public function add(PostTag $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
