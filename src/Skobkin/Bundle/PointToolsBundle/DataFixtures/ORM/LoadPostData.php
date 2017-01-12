<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $om)
    {
        /** @var User $testUser */
        $testUser = $this->getReference('test_user_99999');

        $longPost = (new Post('longpost'))
            ->setAuthor($testUser)
            ->setCreatedAt(new \DateTime())
            ->setText('Test post with many comments')
            ->setPrivate(false)
            ->setType(Post::TYPE_POST)
            ->setDeleted(false)
        ;

        $shortPost = (new Post('shortpost'))
            ->setAuthor($testUser)
            ->setCreatedAt(new \DateTime())
            ->setText('Test short post')
            ->setPrivate(false)
            ->setType(Post::TYPE_POST)
            ->setDeleted(false)
        ;

        $om->persist($longPost);
        $om->persist($shortPost);
        $om->flush();

        $this->addReference('test_post_longpost', $longPost);
    }

    public function getOrder(): int
    {
        return 2;
    }
}