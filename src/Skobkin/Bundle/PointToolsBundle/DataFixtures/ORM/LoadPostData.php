<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\{AbstractFixture, OrderedFixtureInterface};
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\{Blogs\Post, User};

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public const POST_ID_LONG = 'longpost';
    public const POST_ID_SHORT = 'shortpost';
    public const POST_ID_PR_USER = 'prusrpst';
    public const POST_ID_WL_USER = 'wlusrpst';
    public const POST_ID_PR_WL_USER = 'prwlusrpst';

    public function load(ObjectManager $om)
    {
        /** @var User $mainUser */
        $mainUser = $this->getReference('test_user_'.LoadUserData::USER_MAIN_ID);
        /** @var User $privateUser */
        $privateUser = $this->getReference('test_user_'.LoadUserData::USER_PRIV_ID);
        /** @var User $wlUser */
        $wlUser = $this->getReference('test_user_'.LoadUserData::USER_WLON_ID);
        /** @var User $prWlUser */
        $prWlUser = $this->getReference('test_user_'.LoadUserData::USER_PRWL_ID);

        $longPost = (new Post(self::POST_ID_LONG, $mainUser, new \DateTime(), Post::TYPE_POST))
            ->setText('Test post with many comments')
            ->setPrivate(false)
            ->setDeleted(false)
        ;

        $shortPost = (new Post(self::POST_ID_SHORT, $mainUser, new \DateTime(), Post::TYPE_POST))
            ->setText('Test short post')
            ->setPrivate(false)
            ->setDeleted(false)
        ;

        $privateUserPost = (new Post(self::POST_ID_PR_USER, $privateUser, new \DateTime(), Post::TYPE_POST))
            ->setText('Post from private user. Should not be visible in the public feed.')
            ->setPrivate(false)
            ->setDeleted(false)
        ;

        $wlUserPost = (new Post(self::POST_ID_WL_USER, $wlUser, new \DateTime(), Post::TYPE_POST))
            ->setText('Post from whitelist-only user. Should only be visible for whitelisted users.')
            ->setPrivate(false)
            ->setDeleted(false)
        ;

        $privateWlUserPost = (new Post(self::POST_ID_PR_WL_USER, $prWlUser, new \DateTime(), Post::TYPE_POST))
            ->setText('Post from private AND whitelist-only user. Should not be visible in the public feed.')
            ->setPrivate(false)
            ->setDeleted(false)
        ;

        $om->persist($longPost);
        $om->persist($shortPost);
        $om->persist($privateUserPost);
        $om->persist($wlUserPost);
        $om->persist($privateWlUserPost);
        $om->flush();

        $this->addReference('test_post_longpost', $longPost);
    }

    public function getOrder(): int
    {
        return 2;
    }
}