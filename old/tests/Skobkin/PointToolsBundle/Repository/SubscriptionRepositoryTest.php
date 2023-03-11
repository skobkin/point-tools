<?php

namespace Tests\Skobkin\PointToolsBundle\Repository;

use Doctrine\ORM\EntityManager;
use src\PointToolsBundle\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscriptionRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepo;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->subscriptionRepo = $this->em->getRepository('SkobkinPointToolsBundle:Subscription');
    }

    public function testGetUserSubscribersCountById(): int
    {
        $count = $this->subscriptionRepo->getUserSubscribersCountById(99999);

        $this->assertInternalType('int', $count, 'Not integer returned');
        $this->assertGreaterThanOrEqual(2, $count, 'Lesser than 2 subscribers found');
        $this->assertLessThanOrEqual(5, $count, 'More than 5 subscribers found');

        return $count;
    }
}