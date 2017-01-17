<?php

namespace Tests\Skobkin\PointToolsBundle\Event;

use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Entity\UserRenameEvent;
use Skobkin\Bundle\PointToolsBundle\Event\UsersRenamedEvent;

class UsersRenamedEventTest extends \PHPUnit_Framework_TestCase
{
    public function testNameConstant()
    {
        $this->assertEquals('app.users.renamed', UsersRenamedEvent::NAME, 'Event name changed');
    }

    public function testGetRenames()
    {
        $user = new User(99999, new \DateTime(), 'testuser', 'Test User 1');
        $renameRecords = [
            new UserRenameEvent($user, 'testuser_old1'),
            new UserRenameEvent($user, 'testuser_old2'),
            new UserRenameEvent($user, 'testuser_old3'),
        ];

        $renameEvent = new UsersRenamedEvent($renameRecords);

        $this->assertCount(3, $renameEvent->getRenames(), 'Not exactly 3 renames returned');
        $this->assertEquals('testuser_old1', $renameEvent->getRenames()[0]->getOldLogin(), 'Invalid old login returned');
    }
}