<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\Form\UserSearchType;
use Skobkin\Bundle\PointToolsBundle\Repository\{SubscriptionEventRepository, SubscriptionRepository, UserRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class MainController extends AbstractController
{
    const AJAX_AUTOCOMPLETE_SIZE = 10;

    /** @var int */
    private $appUserId;

    /** @var string */
    private $appUserLogin;

    public function __construct(int $appUserId, string $appUserLogin)
    {
        $this->appUserId = $appUserId;
        $this->appUserLogin = $appUserLogin;
    }

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
            'subscribers_count' => $subscriptionRepository->getUserSubscribersCountById($this->appUserId),
            'events_count' => $subscriptionEventRepository->getLastDayEventsCount(),
            'service_login' => $this->appUserLogin,
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
