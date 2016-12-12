<?php

namespace Skobkin\Bundle\PointToolsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class PostControllerTest extends WebTestCase
{
    public function testNonExistingPostPage()
    {
        $client = static::createClient();
        $client->request('GET', '/nonexistingpost');

        $this->assertTrue($client->getResponse()->isNotFound(), '404 response code for non-existing post');
    }

    /**
     * @return Crawler
     */
    public function testShortPostPageIsOk()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/shortpost');

        $this->assertTrue($client->getResponse()->isOk(), '200 response code for existing post');

        return $crawler;
    }

    /**
     * @depends testShortPostPageIsOk
     *
     * @param Crawler $crawler
     *
     * @return Crawler
     */
    public function testShortPostPageHasPostBlock(Crawler $crawler)
    {
        $postBlock = $crawler->filter('div.post-block');

        $this->assertEquals(1, $postBlock->count(), 'Post page has zero or more than one div.post-block');

        return $postBlock->first();
    }

    /**
     * @depends testShortPostPageHasPostBlock
     *
     * @param Crawler $postBlock
     */
    public function testShortPostPostBlockHasCorrectPostText(Crawler $postBlock)
    {
        $postText = $postBlock->filter('div.post-text > div')->first();

        $this->assertEquals(1, $postText->count(), 'Postblock has no .post-text block');
        $p = $postText->filter('p');
        $this->assertEquals(1, $p->count(), '.post-text has zero or more than one paragraphs');
        $this->assertEquals('Test short post', $p->text(), '.post-text has no correct post text');
    }
}
