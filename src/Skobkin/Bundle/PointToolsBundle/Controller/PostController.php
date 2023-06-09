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
     *
     * @return Response
     */
    public function showAction(Post $post, PostRepository $postRepository): Response
    {
        return $this->render('SkobkinPointToolsBundle:Post:show.html.twig', [
            'post' => $postRepository->getPostWithComments($post->getId()),
        ]);
    }

}
