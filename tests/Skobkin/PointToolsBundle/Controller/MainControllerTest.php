<?php

namespace Tests\Skobkin\PointToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testUserSearch()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $userSearchForm = $crawler->filter('form.form-inline')->form();
        $userSearchForm['user_search[login]'] = 'testuser';

        $client->submit($userSearchForm);

        $this->assertTrue($client->getResponse()->isRedirect('/user/testuser'), 'Redirect to testuser\'s page didn\'t happen');
    }

    public function testNonExistingUserSearch()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $userSearchForm = $crawler->filter('form.form-inline')->form();
        $userSearchForm['user_search[login]'] = 'non-existing-user';

        $crawler = $client->submit($userSearchForm);

        $this->assertFalse($client->getResponse()->isRedirection(), 'Redirect to non-existing user on the main page');

        $formElement = $crawler->filter('form.form-inline')->first();

        $this->assertEquals(1, $formElement->count(), 'Form not found after searching non-existing user');

        $loginInputElement = $formElement->filter('#user_search_login')->first();

        $this->assertEquals(1, $loginInputElement->count(), 'Login form input element not found after search of non existing user');

        $errorsListElement = $loginInputElement->siblings()->filter('span.help-block')->children()->filter('ul.list-unstyled')->first();

        $this->assertEquals(1, $errorsListElement->count(), 'Form errors list not found after search of non-existing user');

        $firstError = $errorsListElement->children()->first();

        $this->assertEquals(1, $firstError->count(), 'No errors in the list');
        $this->assertEquals(' Login not found', $firstError->text(), 'Incorrect error text');
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

    /**
     * Tests AJAX user search autocomplete and returns JSON response string
     *
     * @return string
     */
    public function testAjaxUserAutoComplete()
    {
        $client = static::createClient();
        $client->request('GET', '/ajax/users/search/testuser');

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Response has "Content-Type" = "application/json"');

        return $client->getResponse()->getContent();
    }

    /**
     * @depends testAjaxUserAutoComplete
     *
     * @param $json
     */
    public function testAjaxUserAutoCompleteHasOptions($json)
    {
        $data = json_decode($json);

        $this->assertNotNull($data, 'JSON data successfully decoded and not empty');
        $this->assertTrue(is_array($data), 'JSON data is array');
        $this->assertGreaterThan(0, count($data), 'Array has at least one element');

        return $data;
    }

    /**
     * @depends testAjaxUserAutoCompleteHasOptions
     *
     * @param array $users
     */
    public function testAjaxUserAutoCompleteHasValidUserObjects(array $users)
    {
        foreach ($users as $key => $user) {
            $this->assertTrue(array_key_exists('login', $user), sprintf('%d row of result has \'login\' field', $key));
            $this->assertTrue(array_key_exists('name', $user), sprintf('%d row of result has \'name\' field', $key));
        }
    }

    public function testAjaxUserAutoCompleteForNonExistingUser()
    {
        $client = static::createClient();
        $client->request('GET', '/ajax/users/search/aksdjhaskdjhqwhdgqkjwhdgkjah');

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Response has "Content-Type" = "application/json"');

        $data = json_decode($client->getResponse()->getContent());

        $this->assertNotNull($data, 'JSON data successfully decoded and not empty');
        $this->assertTrue(is_array($data), 'JSON data is array');
        $this->assertEquals(0, count($data), 'Array has no elements');
    }
}
