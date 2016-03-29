<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $om)
    {
        $user = (new User(99999, 'testuser', 'Test User'))
            ->setCreatedAt(new \DateTime())
        ;

        $om->persist($user);
        $om->flush();

        $this->addReference('test_user', $user);
    }

    public function getOrder()
    {
        return 1;
    }
}