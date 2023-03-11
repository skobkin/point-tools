<?php

namespace src\PointToolsBundle\Repository\Blogs;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\Blogs\File;

class FileRepository extends EntityRepository
{
    public function add(File $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
