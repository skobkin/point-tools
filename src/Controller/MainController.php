<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\UserSearchType;
use App\Repository\{SubscriptionEventRepository, SubscriptionRepository, UserRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class MainController extends AbstractController
{
    private const AJAX_AUTOCOMPLETE_SIZE = 10;

    public function __construct(
        private readonly int $pointAppUserId,
        private readonly string $pointAppUserLogin,
    ) {
    }

    public function indexAction(
        Request $request,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionEventRepository $subscriptionEventRepository
    ): Response {
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

        return $this->render('Web/index.html.twig', [
            'form' => $form->createView(),
            'autocomplete_size' => self::AJAX_AUTOCOMPLETE_SIZE,
            'users_count' => $userRepository->getUsersCount(),
            'subscribers_count' => $subscriptionRepository->getUserSubscribersCountById($this->pointAppUserId),
            'events_count' => $subscriptionEventRepository->getLastDayEventsCount(),
            'service_login' => $this->pointAppUserLogin,
        ]);
    }

    /** Returns user search autocomplete data in JSON */
    public function searchUserAjaxAction(string $login, UserRepository $userRepository): JsonResponse
    {
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
