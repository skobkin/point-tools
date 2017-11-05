<?php

namespace Skobkin\Bundle\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\UserRenameEvent;

class UserRenameEventRepository extends EntityRepository
{
    public function add(UserRenameEvent $event)
    {
        $this->getEntityManager()->persist($event);
    }
}