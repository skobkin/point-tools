<?php

namespace src\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use src\PointToolsBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public const USER_MAIN_ID = 99999;
    public const USER_SCND_ID = 99998;
    public const USER_PRIV_ID = 99997;
    public const USER_WLON_ID = 99996;
    public const USER_PRWL_ID = 99995;
    public const USER_UNNM_ID = 99994;

    private $users = [
        ['id' => self::USER_MAIN_ID, 'login' => 'testuser', 'name' => 'Test User 1', 'private' => false, 'whitelist-only' => false],
        ['id' => self::USER_SCND_ID, 'login' => 'testuser2', 'name' => 'Test User 2 for autocomplete test', 'private' => false, 'whitelist-only' => false],
        ['id' => self::USER_PRIV_ID, 'login' => 'private_user', 'name' => 'Test User 3', 'private' => true, 'whitelist-only' => false],
        ['id' => self::USER_WLON_ID, 'login' => 'whitelist_only_user', 'name' => 'Test User 4', 'private' => false, 'whitelist-only' => true],
        ['id' => self::USER_PRWL_ID, 'login' => 'private_whitelist_only_user', 'name' => 'Test User 4', 'private' => false, 'whitelist-only' => true],
        ['id' => self::USER_UNNM_ID, 'login' => 'unnamed_user', 'name' => null, 'private' => false, 'whitelist-only' => false],
    ];

    public function load(ObjectManager $om)
    {
        foreach ($this->users as $userData) {
            $user = new User($userData['id'], new \DateTime(), $userData['login'], $userData['name']);
            $user->updatePrivacy(!$userData['private'], $userData['whitelist-only']);

            $om->persist($user);

            $this->addReference('test_user_'.$user->getId(), $user);
        }

        $om->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}