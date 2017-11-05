<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\Form\UserSearchType;
use Skobkin\Bundle\PointToolsBundle\Repository\{SubscriptionEventRepository, SubscriptionRepository, UserRepository};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class MainController extends Controller
{
    const AJAX_AUTOCOMPLETE_SIZE = 10;

    public function indexAction(
        Request $request,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionEventRepository $subscriptionEventRepository
    ): Response {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(
            UserSearchType::class,
            null,
            [
                'action' => $this->generateUrl('index'),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $login = $form->get('login')->getData();

            if (null !== $user = $userRepository->findOneBy(['login' => $login])) {
                return $this->redirectToRoute('user_show', ['login' => $login]);
            }

            $form->get('login')->addError(new FormError('Login not found'));
        }

        return $this->render('SkobkinPointToolsBundle:Main:index.html.twig', [
            'form' => $form->createView(),
            'autocomplete_size' => self::AJAX_AUTOCOMPLETE_SIZE,
            'users_count' => $userRepository->getUsersCount(),
            'subscribers_count' => $subscriptionRepository->getUserSubscribersCountById($this->getParameter('point_id')),
            'events_count' => $subscriptionEventRepository->getLastDayEventsCount(),
            'service_login' => $this->getParameter('point_login'),
        ]);
    }

    /**
     * Returns user search autocomplete data in JSON
     *
     * @param string $login
     *
     * @return JsonResponse
     */
    public function searchUserAjaxAction(string $login, UserRepository $userRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $result = [];

        foreach ($userRepository->findUsersLikeLogin($login, self::AJAX_AUTOCOMPLETE_SIZE) as $user) {
            $result[] = [
                'login' => $user->getLogin(),
                'name' => $user->getName(),
            ];
        }

        return $this->json($result);
    }
}
