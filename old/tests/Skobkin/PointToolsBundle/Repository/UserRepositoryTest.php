<?php

namespace Tests\Skobkin\PointToolsBundle\Repository;

use Doctrine\ORM\EntityManager;
use src\PointToolsBundle\DTO\TopUserDTO;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UserRepository
     */
    private $userRepo;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepo = $this->em->getRepository('SkobkinPointToolsBundle:User');
    }

    public function testFindAll()
    {
        $users = $this->userRepo->findAll();

        $this->assertCount(6, $users, 'Not exactly 6 users in the databas');
    }

    public function testFindOneByLogin()
    {
        /** @var User $user */
        $user = $this->userRepo->findOneBy(['login' => 'testuser']);

        $this->assertNotNull($user, 'testuser not found');
        $this->assertEquals(99999, $user->getId());
        $this->assertEquals('Test User 1', $user->getName());
    }

    public function testFindUserByLogin()
    {
        $testUser = $this->userRepo->findUserByLogin('testuser');

        $this->assertNotNull($testUser, 'Testuser not found in repository');
        $this->assertEquals(99999, $testUser->getId(), 'Testuser ID is not 99999');
    }

    public function testFindUsersLikeLogin()
    {
        // Searching LIKE %stus% (testuserX)
        $users = $this->userRepo->findUsersLikeLogin('stus');

        $this->assertCount(2, $users, 'Repository found not exactly 5 users');
    }

    public function testGetUsersCount()
    {
        $count = $this->userRepo->getUsersCount();

        $this->assertEquals(6, $count, 'Counted not exactly 5 users');
    }

    public function testFindUserSubscribersById()
    {
        $subscribers = $this->userRepo->findUserSubscribersById(99999);
        $this->assertCount(4, $subscribers, 'Not exactly 4 subscribers found for user#99999');

        $subscribers = $this->userRepo->findUserSubscribersById(99998);
        $this->assertCount(2, $subscribers, 'Not exactly 2 subscribers found for user#99998');

        $subscribers = $this->userRepo->findUserSubscribersById(99997);
        $this->assertCount(1, $subscribers, 'Not exactly 1 subscriber found for user#99997');

        $subscribers = $this->userRepo->findUserSubscribersById(99996);
        $this->assertCount(0, $subscribers, 'Not exactly 0 subscribers found for user#99996');

        $subscribers = $this->userRepo->findUserSubscribersById(99995);
        $this->assertCount(0, $subscribers, 'Not exactly 0 subscribers found for user#99995');

    }

    public function testGetTopUsers()
    {
        $topUsers = $this->userRepo->getTopUsers();

        $this->assertCount(3, $topUsers, 'Found not exactly 3 top users');

        foreach ($topUsers as $topUser) {
            $this->assertEquals(TopUserDTO::class, get_class($topUser), 'Invalid type returned');
        }
    }
}