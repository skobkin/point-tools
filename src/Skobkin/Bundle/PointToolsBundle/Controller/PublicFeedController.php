<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublicFeedController extends Controller
{
    private const POSTS_PER_PAGE = 20;

    public function indexAction(Request $request)
    {
        // @todo autowire
        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        $paginator = $this->get('knp_paginator');

        $postsPagination = $paginator->paginate(
            $postRepository->createPublicFeedPostsQuery(),
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        return $this->render(
            'SkobkinPointToolsBundle:Post:feed.html.twig',
            [
                // @todo Move to translation
                'feed_title' => 'All',
                'posts' => $postsPagination,
                // Special feed mark (to not show comments and other)
                'is_feed' => true,
            ]
        );
    }
}
