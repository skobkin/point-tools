<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * @ParamConverter("post", class="SkobkinPointToolsBundle:Blogs\Post")
     *
     * @return Response
     */
    public function showAction(Post $post): Response
    {
        return $this->render('SkobkinPointToolsBundle:Post:show.html.twig', [
            'post' => $this->getDoctrine()->getRepository('SkobkinPointToolsBundle:Blogs\Post')->getPostWithComments($post->getId()),
        ]);
    }

}
