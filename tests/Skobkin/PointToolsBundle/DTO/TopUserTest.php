<?php

namespace Tests\Skobkin\PointToolsBundle\DTO;

use Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO;

class TopUserTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        return new TopUserDTO('testuser', 3);
    }

    /**
     * @depends testCreate
     */
    public function testGetLogin(TopUserDTO $topUser)
    {
        $this->assertInternalType('string', $topUser->getLogin());
        $this->assertEquals('testuser', $topUser->getLogin());
    }

    /**
     * @depends testCreate
     */
    public function testGetSubscribersCount(TopUserDTO $topUser)
    {
        $this->assertInternalType('int', $topUser->getSubscribersCount());
        $this->assertEquals(3, $topUser->getSubscribersCount());
    }
}