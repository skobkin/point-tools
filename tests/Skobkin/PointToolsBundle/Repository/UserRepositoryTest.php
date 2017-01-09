<?php

namespace Tests\Skobkin\PointToolsBundle\Repository;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
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

        $this->assertCount(5, $users, 'Not exactly 5 users in the databas');
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

        $this->assertCount(5, $users, 'Repository found not exactly 5 users');
    }

    public function testGetUsersCount()
    {
        $count = $this->userRepo->getUsersCount();

        $this->assertEquals(5, $count, 'Counted not exactly 5 users');
    }

    public function testFindUserSubscribersById()
    {
        $subscribers = $this->userRepo->findUserSubscribersById(99999);

        $this->assertGreaterThanOrEqual(2, count($subscribers), 'Less than 2 subscribers found');
        $this->assertLessThanOrEqual(5, count($subscribers), 'More than 5 subscribers found');
    }

    public function testGetTopUsers()
    {
        $topUsers = $this->userRepo->getTopUsers();

        $this->assertCount(5, $topUsers, 'Found not exactly 5 top users');

        foreach ($topUsers as $topUser) {
            $this->assertEquals(TopUserDTO::class, get_class($topUser), 'Invalid type returned');
        }
    }
}