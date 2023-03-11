<?php

namespace src\PointToolsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use src\PointToolsBundle\Entity\Blogs\Comment;
use src\PointToolsBundle\Entity\Blogs\Post;
use src\PointToolsBundle\Entity\User;

class LoadCommentsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $om)
    {
        /** @var Post $post */
        $post = $this->getReference('test_post_longpost');

        /** @var User[] $users */
        $users = [
            $this->getReference('test_user_99999'),
            $this->getReference('test_user_99998'),
            $this->getReference('test_user_99997'),
            $this->getReference('test_user_99996'),
            $this->getReference('test_user_99995'),
        ];

        $comments = [];

        foreach (range(1, 10000) as $num) {
            $comment = (new Comment())
                ->setNumber($num)
                ->setDeleted(mt_rand(0, 15) ? false : true)
                ->setCreatedAt(new \DateTime())
                ->setAuthor($users[array_rand($users)])
                ->setRec(false)
                ->setText(
                    'Some text with [link to @skobkin-ru site](https://skobk.in/) and `code block`'.PHP_EOL.
                    'and some quotation:'.PHP_EOL.
                    '> test test quote'.PHP_EOL.
                    'and some text after'
                )
            ;

            if (count($comments) > 0 && mt_rand(0, 1)) {
                $comment->setParent($comments[mt_rand(0, count($comments) - 1)]);
            }

            $post->addComment($comment);
            $comments[] = $comment;

            $om->persist($comment);
        }

        $om->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}