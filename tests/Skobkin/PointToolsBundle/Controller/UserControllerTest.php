<?php

namespace Tests\Skobkin\PointToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class UserControllerTest extends WebTestCase
{
    public function testTestuserPageHasHeading()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/testuser');

        $userLoginHeading = $crawler->filter('h1#user-login')->first();

        $this->assertEquals(
            1,
            $userLoginHeading->count(),
            'User page has no heading element with user login and avatar'
        );

        return $userLoginHeading;
    }

    /**
     * @depends testTestuserPageHasHeading
     *
     * @param Crawler $heading
     */
    public function testTestuserPageHasUserLink(Crawler $heading)
    {
        $userLink = $heading->children()->filter('a');

        $this->assertEquals(
            1,
            $userLink->count(),
            'User page has no user link in the heading'
        );

        $this->assertEquals(
            'testuser',
            $userLink->text(),
            'User link text is not equal user login'
        );
    }

    /**
     * @depends testTestuserPageHasHeading
     *
     * @param Crawler $heading
     */
    public function testTestuserPageHasUserAvatar(Crawler $heading)
    {
        $userAvatar = $heading->children()->filter('img')->first();

        $this->assertEquals(
            1,
            $userAvatar->count(),
            'testuser page has no avatar'
        );

        $pointScheme = static::createClient()->getContainer()->getParameter('point_scheme');

        $this->assertEquals(
            $pointScheme.'://point.im/avatar/testuser/80',
            $userAvatar->attr('src'),
            'testuser avatar image source is not correct'
        );
    }

    public function testTestuserHasSubscribers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/testuser');

        $subscribersList = $crawler->filter('.user-subscribers ul.users')->first();

        $this->assertEquals(
            1,
            $subscribersList->count(),
            'testuser has no subscribers list shown on the page'
        );

        $this->assertGreaterThan(
            0,
            $subscribersList->children()->count(),
            'Testuser has zero subscribers in the list'
        );
    }
}
