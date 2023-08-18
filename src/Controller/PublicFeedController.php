<?php
declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Repository\Blog\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};

class PublicFeedController extends AbstractController
{
    private const POSTS_PER_PAGE = 20;

    public function index(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $postsPagination = $paginator->paginate(
            $postRepository->createPublicFeedPostsQuery(),
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        return $this->render(
            'Web/Post/feed.html.twig',
            [
                'posts' => $postsPagination,
                // Special feed mark (to not show comments and other)
                'is_feed' => true,
            ]
        );
    }
}
