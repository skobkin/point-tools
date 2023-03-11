<?php

namespace src\PointToolsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use src\PointToolsBundle\Entity\UserRenameEvent;

class UserRenameEventRepository extends EntityRepository
{
    public function add(UserRenameEvent $event)
    {
        $this->getEntityManager()->persist($event);
    }
}