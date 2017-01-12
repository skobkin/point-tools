<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\File;

class FileRepository extends EntityRepository
{
    public function add(File $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
