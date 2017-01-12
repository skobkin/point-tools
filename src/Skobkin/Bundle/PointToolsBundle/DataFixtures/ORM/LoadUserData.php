<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    private $users = [
        // 99999
        ['login' => 'testuser', 'name' => 'Test User 1'],
        // 99998
        ['login' => 'testuser2', 'name' => 'Test User 2'],
        // 99997
        ['login' => 'testuser3', 'name' => 'Test User 3'],
        // 99996
        ['login' => 'testuser4', 'name' => 'Test User 4'],
        //99995
        ['login' => 'testuser5', 'name' => null],
    ];

    public function load(ObjectManager $om)
    {
        $userId = 99999;

        foreach ($this->users as $userData) {
            $user = (new User($userId--, $userData['login'], $userData['name']))
                ->setCreatedAt(new \DateTime())
            ;

            $om->persist($user);

            $this->addReference('test_user_'.$user->getId(), $user);
        }

        $om->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}