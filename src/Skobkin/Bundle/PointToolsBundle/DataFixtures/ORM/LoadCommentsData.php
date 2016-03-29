<?php

namespace Skobkin\Bundle\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

class LoadCommentsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $om)
    {
        /** @var Post $post */
        $post = $this->getReference('test_post');

        /** @var User $user */
        $user = $this->getReference('test_user');

        $comments = [];

        foreach (range(1, 10000) as $num) {
            $comment = (new Comment())
                ->setNumber($num)
                ->setDeleted(rand(0, 15) ? false : true)
                ->setCreatedAt(new \DateTime())
                ->setAuthor($user)
                ->setRec(false)
                ->setText('Some text with [link to @skobkin-ru site](https://skobk.in/) and `code block`
and some quotation:
> test test quote
and some text after')
            ;

            if (count($comments) > 0 && rand(0, 1)) {
                $comment->setParent($comments[rand(0, count($comments) - 1)]);
            }

            $post->addComment($comment);
            $comments[] = $comment;

            $om->persist($comment);
        }

        $om->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}