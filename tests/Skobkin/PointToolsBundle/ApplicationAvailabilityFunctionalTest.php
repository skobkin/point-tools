<?php

namespace Tests\Skobkin\PointToolsBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return [
            'index_page' => ['/'],
            'top_page' => ['/users/top'],
            'last_events_page' => ['/events/last'],
            'test_user_page' => ['/user/testuser']
        ];
    }
}