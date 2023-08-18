<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Blog\Post;
use App\Repository\Blog\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function show(Post $post, PostRepository $postRepository): Response
    {
        if ($post->getAuthor()->isPrivate()) {
            /**
             * Throwing 404 instead of 403 because of
             * @see \Symfony\Component\Security\Http\Firewall\ExceptionListener::handleAccessDeniedException()
             * starts to replace 403 by 401 exceptions for anonymous users and tries to authenticate them.
             */
            throw $this->createNotFoundException('Author\'s blog is private.');
            //throw $this->createAccessDeniedException('Author\'s blog is private.');
        }

        return $this->render('Web/Post/show.html.twig', [
            'post' => $postRepository->getPostWithComments($post->getId()),
        ]);
    }
}
