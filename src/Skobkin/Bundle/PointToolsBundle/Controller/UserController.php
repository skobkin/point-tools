<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param string $login
     */
    public function showAction($login)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('SkobkinPointToolsBundle:User')->findUserByLogin($login);

        if (!$user) {
            throw $this->createNotFoundException('User ' . $login . ' not found.');
        }

        $userApi = $this->container->get('skobkin_point_tools.api_user');

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $em->getRepository('SkobkinPointToolsBundle:User')->findUserSubscribersById($user->getId()),
            'log' => $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getUserLastSubscriptionEventsById($user, 10),
            'avatar_url' => $userApi->getAvatarUrl($user, UserApi::AVATAR_SIZE_LARGE),
        ]);
    }

    public function topAction()
    {
        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'top_users' => $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:User')->getTopUsers(),
        ]);
    }

    /**
     * @param Request $request
     */
    public function searchUserAction(Request $request)
    {
        $login = $request->request->get('login');

        if (!$login) {
            return $this->redirectToRoute('index');
        }
        return $this->redirectToRoute('user_show', ['login' => $login]);
    }
}
