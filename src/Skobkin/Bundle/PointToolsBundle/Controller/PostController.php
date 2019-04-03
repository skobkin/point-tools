<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @ParamConverter("post", class="SkobkinPointToolsBundle:Blogs\Post")
     */
    public function showAction(Post $post, PostRepository $postRepository): Response
    {
        if ((!$post->getAuthor()->isPublic()) || $post->getAuthor()->isWhitelistOnly()) {
            throw $this->createAccessDeniedException('Author\'s blog is private.');
        }

        return $this->render('SkobkinPointToolsBundle:Post:show.html.twig', [
            'post' => $postRepository->getPostWithComments($post->getId()),
        ]);
    }

}
