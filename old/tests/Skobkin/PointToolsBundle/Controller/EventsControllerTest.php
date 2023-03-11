<?php

namespace Tests\Skobkin\PointToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventsControllerTest extends WebTestCase
{
    public function testHasEvents()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/events/last');

        // Checking that we have events table
        $this->assertEquals(
            1,
            $crawler->filter('.last-subscriptions-log table')->count(),
            'Has no events table'
        );

        // Checking that we have at least one event in the table
        $this->assertGreaterThan(
            0,
            $crawler->filter('.last-subscriptions-log table')->children()->filter('tbody')->children()->count(),
            'No events shown in the table'
        );
    }

    // @todo test pagination
}