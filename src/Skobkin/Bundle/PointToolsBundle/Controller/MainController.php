<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\Form\UserSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    const AJAX_AUTOCOMPLETE_SIZE = 10;

    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(
            new UserSearchType(),
            null,
            [
                'action' => $this->generateUrl('index'),
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $login = $form->get('login')->getData();

            if (null !== $user = $em->getRepository('SkobkinPointToolsBundle:User')->findOneBy(['login' => $login])) {
                return $this->redirectToRoute('user_show', ['login' => $login]);
            }

            $form->get('login')->addError(new FormError('Login not found'));
        }

        return $this->render('SkobkinPointToolsBundle:Main:index.html.twig', [
            'form' => $form->createView(),
            'autocomplete_size' => self::AJAX_AUTOCOMPLETE_SIZE,
            'users_count' => $em->getRepository('SkobkinPointToolsBundle:User')->getUsersCount(),
            'subscribers_count' => $em->getRepository('SkobkinPointToolsBundle:Subscription')->getUserSubscribersCountById($this->container->getParameter('point_id')),
            'events_count' => $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getLastDayEventsCount(),
            'service_login' => $this->container->getParameter('point_login'),
        ]);
    }

    /**
     * Returns user search autocomplete data in JSON
     *
     * @param $login
     *
     * @return JsonResponse
     */
    public function searchUserAjaxAction($login)
    {
        $em = $this->getDoctrine()->getManager();

        $result = [];

        foreach ($em->getRepository('SkobkinPointToolsBundle:User')->findUsersLikeLogin($login, self::AJAX_AUTOCOMPLETE_SIZE) as $user) {
            $result[] = [
                'login' => $user->getLogin(),
                'name' => $user->getName(),
            ];
        }

        return new JsonResponse($result);
    }
}
