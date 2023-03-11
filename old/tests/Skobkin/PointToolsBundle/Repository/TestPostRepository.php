<?php

namespace Tests\Skobkin\PointToolsBundle\Repository;

use Doctrine\ORM\EntityManager;
use src\PointToolsBundle\Entity\Blogs\Post;
use src\PointToolsBundle\Repository\Blogs\PostRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestPostRepository extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PostRepository
     */
    private $postRepo;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->postRepo = $this->em->getRepository('SkobkinPointToolsBundle:Blogs\Post');
    }

    public function testFindOneById()
    {
        /** @var Post $post */
        $post = $this->postRepo->findOneBy(['id' => 'shortpost']);

        $this->assertNotNull($post, 'Post #shortpost not found');
        $this->assertEquals('shortpost', $post->getId(), 'Post id is not #shortpost');
        $this->assertEquals(99999, $post->getAuthor()->getId(), 'Post author id is not 99999');
        $this->assertEquals('Test post with many comments', $post->getText(), 'Invalid post text');
        $this->assertFalse($post->getPrivate(), 'Post is private');
    }

    public function testFindAll()
    {
        /** @var Post[] $posts */
        $posts = $this->postRepo->findAll();

        $this->assertCount(2, $posts, 'Not exactly 2 posts found');
    }

    public function testGetPostWithComments()
    {
        /** @var Post $longPost */
        $longPost = $this->postRepo->getPostWithComments('longpost');

        $this->assertNotNull($longPost, '#longpost not found');
        $this->assertEquals(99999, $longPost->getAuthor()->getId(), 'Post author ID is not 99999');
        $this->assertEquals('testuser', $longPost->getAuthor()->getLogin(), 'Post author login is not testuser');
        $this->assertCount(10000, $longPost->getComments(), 'Not exactly 10000 comments found');
    }
}