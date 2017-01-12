<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment;

class CommentRepository extends EntityRepository
{
    public function add(Comment $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}