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
    public function load(ObjectManager $om): void
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

        $text = 'Some text with [link to @skobkin-ru site](https://skobk.in/) and `code block`'.PHP_EOL.
            'and some quotation:'.PHP_EOL.
            '> test test quote'.PHP_EOL.
            'and some text after';

        foreach (range(1, 10000) as $num) {
            $comment = new Comment(
                $text,
                new \DateTime(),
                false,
                $post,
                $num,
                ($num > 1 && !random_int(0, 4)) ? random_int(1, $num - 1) : null,
                $users[array_rand($users)],
                []
            );

            if (!random_int(0, 15)) {
                $comment->delete();
            }

            $post->addComment($comment);

            $om->persist($comment);
        }

        $om->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}