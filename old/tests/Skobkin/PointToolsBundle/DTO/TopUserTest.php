<?php
declare(strict_types=1);

namespace Tests\Skobkin\PointToolsBundle\DTO;

use App\DTO\TopUserDTO;

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
        $this->assertInternalType('string', $topUser->login);
        $this->assertEquals('testuser', $topUser->login);
    }

    /**
     * @depends testCreate
     */
    public function testGetSubscribersCount(TopUserDTO $topUser)
    {
        $this->assertInternalType('int', $topUser->subscribersCount);
        $this->assertEquals(3, $topUser->subscribersCount);
    }
}
