<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PublicFeedController extends AbstractController
{
    private const POSTS_PER_PAGE = 20;

    public function indexAction(Request $request, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $postsPagination = $paginator->paginate(
            $postRepository->createPublicFeedPostsQuery(),
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        return $this->render(
            'SkobkinPointToolsBundle:Post:feed.html.twig',
            [
                // @todo Move to translation
                'feed_title' => 'Public feed',
                'posts' => $postsPagination,
                // Special feed mark (to not show comments and other)
                'is_feed' => true,
            ]
        );
    }
}
