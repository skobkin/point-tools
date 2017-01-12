<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\PostTag;

class PostTagRepository extends EntityRepository
{
    public function add(PostTag $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
