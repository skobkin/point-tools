<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use JMS\Serializer\Serializer;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Auth;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\UserNotFoundException;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class UserApi extends AbstractApi
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(ClientInterface $httpClient, LoggerInterface $logger, UserFactory $userFactory, Serializer $serializer)
    {
        parent::__construct($httpClient, $logger);

        $this->userFactory = $userFactory;
        $this->serializer = $serializer;
    }

    public function isAuthDataValid(string $login, string $password): bool
    {
        $this->logger->info('Checking user auth data via point.im API');

        $auth = $this->authenticate($login, $password);

        if (null === $auth->getError() && null !== $auth->getToken()) {
            $this->logger->debug('Authentication successfull. Logging out.');

            $this->logout($auth);

            return true;
        }

        return false;
    }

    public function authenticate(string $login, string $password): Auth
    {
        $this->logger->debug('Trying to authenticate user via Point.im API', ['login' => $login]);

        try {
            $authData = $this->getPostRequestData(
                '/api/login',
                [
                    'login' => $login,
                    'password' => $password,
                ]
            );

            return $this->serializer->deserialize($authData, Auth::class, 'json');
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new InvalidResponseException('API method not found', 0, $e);
            } else {
                throw $e;
            }
        }
    }

    public function logout(Auth $auth): bool
    {
        $this->logger->debug('Trying to log user out via Point.im API');

        try {
            $this->getPostRequestData('/api/logout', ['csrf_token' => $auth->getCsRfToken()]);

            return true;
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new InvalidResponseException('API method not found', 0, $e);
            } elseif (Response::HTTP_FORBIDDEN === $e->getResponse()->getStatusCode()) {
                return true;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Get user subscribers by user login
     *
     * @param string $login
     *
     * @return User[]
     *
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscribersByLogin(string $login): array
    {
        $this->logger->debug('Trying to get user subscribers by login', ['login' => $login]);

        try {
            $usersList = $this->getGetRequestData('/api/user/'.urlencode($login).'/subscribers', [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscribers by user id
     *
     * @param int $id
     *
     * @return User[]
     *
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscribersById(int $id): array
    {
        $this->logger->debug('Trying to get user subscribers by id', ['id' => $id]);

        try {
            $usersList = $this->getGetRequestData('/api/user/id/'.(int) $id.'/subscribers', [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscriptions by user login
     *
     * @param string $login
     *
     * @return User[]
     *
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscriptionsByLogin(string $login): array
    {
        $this->logger->debug('Trying to get user subscriptions by login', ['login' => $login]);

        try {
            $usersList = $this->getGetRequestData('/api/user/'.urlencode($login).'/subscriptions', [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscriptions by user id
     *
     * @param int $id
     *
     * @return User[]
     *
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscriptionsById(int $id): array
    {
        $this->logger->debug('Trying to get user subscriptions by id', ['id' => $id]);

        try {
            $usersList = $this->getGetRequestData('/api/user/id/'.(int) $id.'/subscriptions', [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get single user by login
     *
     * @param string $login
     *
     * @return User
     *
     * @throws UserNotFoundException
     * @throws RequestException
     */
    public function getUserByLogin(string $login): User
    {
        $this->logger->debug('Trying to get user by login', ['login' => $login]);

        try {
            $userInfo = $this->getGetRequestData('/api/user/login/'.urlencode($login), [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUserFromUserInfo($userInfo);
    }

    /**
     * Get single user by id
     *
     * @param int $id
     *
     * @return User
     *
     * @throws UserNotFoundException
     * @throws RequestException
     */
    public function getUserById(int $id): User
    {
        $this->logger->debug('Trying to get user by id', ['id' => $id]);

        try {
            $userInfo = $this->getGetRequestData('/api/user/id/'.$id, [], true);
        } catch (RequestException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUserFromUserInfo($userInfo);
    }

    /**
     * Finds and updates or create new user from API response data
     *
     * @param array $userInfo
     *
     * @return User
     *
     * @throws ApiException
     * @throws InvalidResponseException
     */
    private function getUserFromUserInfo(array $userInfo): User
    {
        $this->logger->debug('Trying to create user from array', ['array' => $userInfo]);

        return $this->userFactory->createFromArray($userInfo);
    }

    /**
     * Get array of User objects from API response containing user list
     *
     * @param array $users
     *
     * @return User[]
     *
     * @throws ApiException
     * @throws InvalidResponseException
     */
    private function getUsersFromList(array $users = []): array
    {
        $this->logger->debug('Trying to create multiple users from array', ['array' => $users]);

        if (array_key_exists('error', $users)) {
            $this->logger->error('User list contains error object', ['error' => $users['error']]);

            throw new ApiException('User list response contains error object');
        }

        /** @var User[] $resultUsers */
        $resultUsers = [];

        foreach ($users as $userInfo) {
            $resultUsers[] = $this->getUserFromUserInfo($userInfo);
        }

        return $resultUsers;
    }
}
