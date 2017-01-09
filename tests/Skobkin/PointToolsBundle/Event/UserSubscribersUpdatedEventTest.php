<?php

namespace Tests\Skobkin\PointToolsBundle\Event;

use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Event\UserSubscribersUpdatedEvent;

class UserSubscribersUpdatedEventTest extends \PHPUnit_Framework_TestCase
{
    public function testNameConstant()
    {
        $this->assertEquals('app.user.subscribers_updated', UserSubscribersUpdatedEvent::NAME, 'Event name changed');
    }

    public function testCreate()
    {
        $user = new User(99999, 'testuser', 'Test User 1');

        $subscribed = [
            new User(99998, 'testuser2', 'Test User 2'),
        ];

        $unsubscribed = [
            new User(99997, 'testuser3', 'Test User 3'),
            new User(99996, 'testuser4', 'Test User 4'),
        ];

        return new UserSubscribersUpdatedEvent($user, $subscribed, $unsubscribed);
    }

    /**
     * @depends testCreate
     */
    public function testGetUser(UserSubscribersUpdatedEvent $event)
    {
        $this->assertNotNull($event->getUser(), 'User cannot be extracted from event');
        $this->assertEquals('testuser', $event->getUser()->getLogin(), 'Invalid user login extracted');
        $this->assertEquals('Test User 1', $event->getUser()->getName(), 'Invalid user name extracted');
    }

    /**
     * @depends testCreate
     */
    public function testGetSubscribedUsers(UserSubscribersUpdatedEvent $event)
    {
        $this->assertInternalType('array', $event->getSubscribedUsers(), 'Invalid type returned');
        $this->assertCount(1, $event->getSubscribedUsers(), 'Not exactly 1 subscribed user extracted');
        $this->assertEquals('testuser2', $event->getSubscribedUsers()[0]->getLogin(), 'Invalid subscriber login extracted');
    }

    /**
     * @depends testCreate
     */
    public function testGetUnsubscribedUsers(UserSubscribersUpdatedEvent $event)
    {
        $this->assertInternalType('array', $event->getUnsubscribedUsers(), 'Invalid type returned');
        $this->assertCount(2, $event->getUnsubscribedUsers(), 'Not exactly 2 unsubscribed user extracted');
        $this->assertEquals('testuser3', $event->getUnsubscribedUsers()[0]->getLogin(), 'Invalid unsubscriber login extracted');
    }
}