<?php

namespace Skobkin\Bundle\PointToolsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testUserSearch()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $userSearchForm = $crawler->filter('form.form-inline')->form();

        $userSearchForm['skobkin_bundle_pointtoolsbundle_user_search[login]'] = 'testuser';

        $crawler = $client->submit($userSearchForm);

        $this->assertTrue($client->getResponse()->isRedirect('/user/testuser'), 'Redirect to testuser\'s page didn\'t happen');
    }

    public function testUserStats()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $userStatsBlock = $crawler->filter('.container.service-stats');

        // Assuming we have stats block
        $this->assertEquals(1, $userStatsBlock->count(), 'Stats block not found');
        // @todo rewrite to named classes
        // Assuming we have at least one user shown
        $this->assertGreaterThan(
            0,
            $userStatsBlock->children()->first()->children()->last()->text(),
            'Zero service users shown on the main page'
        );
        // Assuming we have at least one subscriber
        $this->assertGreaterThan(
            0,
            $userStatsBlock->children()->eq(1)->children()->last()->text(),
            'Zero service subscribers shows on the main page'
        );
    }
}
