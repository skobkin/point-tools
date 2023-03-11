<?php

namespace src\PointToolsBundle\Service\Api;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\{
    DeserializationContext, SerializerInterface
};
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\{src\PointToolsBundle\DTO\Api\Auth, src\PointToolsBundle\DTO\Api\User as UserDTO};
use src\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\{
    src\PointToolsBundle\Exception\Api\ForbiddenException, src\PointToolsBundle\Exception\Api\InvalidResponseException, src\PointToolsBundle\Exception\Api\NotFoundException, src\PointToolsBundle\Exception\Api\UserNotFoundException};
use src\PointToolsBundle\Service\Api\AbstractApi;
use src\PointToolsBundle\Service\Factory\UserFactory;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class UserApi extends AbstractApi
{
    private const PREFIX = '/api/user/';

    /**
     * @var UserFactory
     */
    private $userFactory;

    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer, LoggerInterface $logger, UserFactory $userFactory)
    {
        parent::__construct($httpClient, $serializer, $logger);

        $this->userFactory = $userFactory;
    }

    public function isLoginAndPasswordValid(string $login, string $password): bool
    {
        $this->logger->info('Checking user auth data via point.im API');

        $auth = $this->authenticate($login, $password);

        if ($auth->isValid()) {
            $this->logger->debug('Authentication successfull. Logging out.');

            $this->logout($auth);

            return true;
        }

        return false;
    }

    public function authenticate(string $login, string $password): \src\PointToolsBundle\DTO\Api\Auth
    {
        $this->logger->debug('Trying to authenticate user via Point.im API', ['login' => $login]);

        try {
            return $this->getPostJsonData(
                '/api/login',
                [
                    'login' => $login,
                    'password' => $password,
                ],
                \src\PointToolsBundle\DTO\Api\Auth::class
            );
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\InvalidResponseException('API method not found', 0, $e);
        }
    }

    /**
     * @throws \src\PointToolsBundle\Exception\Api\InvalidResponseException
     */
    public function logout(\src\PointToolsBundle\DTO\Api\Auth $auth): bool
    {
        $this->logger->debug('Trying to log user out via Point.im API');

        try {
            $this->getPostResponseBody('/api/logout', ['csrf_token' => $auth->getCsRfToken()]);

            return true;
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\InvalidResponseException('API method not found', 0, $e);
        } catch (\src\PointToolsBundle\Exception\Api\ForbiddenException $e) {
            return true;
        }
    }

    /**
     * Get user subscribers by user login
     *
     * @return User[]
     *
     * @throws \src\PointToolsBundle\Exception\Api\UserNotFoundException
     */
    public function getUserSubscribersByLogin(string $login): array
    {
        $this->logger->debug('Trying to get user subscribers by login', ['login' => $login]);

        try {
            $usersList = $this->getGetJsonData(
                self::PREFIX.urlencode($login).'/subscribers',
                [],
                'array<'.UserDTO::class.'>',
                DeserializationContext::create()->setGroups(['user_short'])
            );
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\UserNotFoundException('User not found', 0, $e, null, $login);
        }

        return $this->userFactory->findOrCreateFromDTOArray($usersList);
    }

    /**
     * Get user subscribers by user id
     *
     * @return User[]
     *
     * @throws \src\PointToolsBundle\Exception\Api\UserNotFoundException
     */
    public function getUserSubscribersById(int $id): array
    {
        $this->logger->debug('Trying to get user subscribers by id', ['id' => $id]);

        try {
            $usersList = $this->getGetJsonData(
                self::PREFIX.'id/'.$id.'/subscribers',
                [],
                'array<'.UserDTO::class.'>',
                DeserializationContext::create()->setGroups(['user_short'])
            );
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\UserNotFoundException('User not found', 0, $e, $id);
        }

        return $this->userFactory->findOrCreateFromDTOArray($usersList);
    }

    /**
     * Get full user info by login
     */
    public function getUserByLogin(string $login): User
    {
        $this->logger->debug('Trying to get user by login', ['login' => $login]);

        try {
            /** @var UserDTO $userInfo */
            $userInfo = $this->getGetJsonData(
                self::PREFIX.'login/'.urlencode($login),
                [],
                UserDTO::class,
                DeserializationContext::create()->setGroups(['user_full'])
            );
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\UserNotFoundException('User not found', 0, $e, null, $login);
        }

        return $this->userFactory->findOrCreateFromDTO($userInfo);
    }

    /**
     * Get full user info by id
     */
    public function getUserById(int $id): User
    {
        $this->logger->debug('Trying to get user by id', ['id' => $id]);

        try {
            /** @var UserDTO $userData */
            $userData = $this->getGetJsonData(
                self::PREFIX.'id/'.$id,
                [],
                UserDTO::class,
                DeserializationContext::create()->setGroups(['user_full'])
            );
        } catch (\src\PointToolsBundle\Exception\Api\NotFoundException $e) {
            throw new \src\PointToolsBundle\Exception\Api\UserNotFoundException('User not found', 0, $e, $id);
        }
        // Not catching ForbiddenException right now

        return $this->userFactory->findOrCreateFromDTO($userData);
    }
}
