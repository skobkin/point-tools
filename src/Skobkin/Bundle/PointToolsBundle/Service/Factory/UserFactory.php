<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\User as UserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Exception\Factory\InvalidUserDataException;
use Skobkin\Bundle\PointToolsBundle\Service\Api\UserApi;

class UserFactory extends AbstractFactory
{
    public const DATE_FORMAT = 'Y-m-d_H:i:s';

    /** @var UserRepository */
    private $userRepository;

    /** @var UserApi */
    private $userApi;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, UserApi $userApi)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->userApi = $userApi;
    }

    public function findOrCreateByLogin(string $login, bool $retrieveMissingFromApi = true): User
    {
        /** @var User $user */
        if (null === $user = $this->userRepository->findBy(['login' => $login])) {
            if ($retrieveMissingFromApi) {
                $user = $this->userApi->getUserByLogin($login);
            } else {
                // TODO neen more specific exception
                throw new \RuntimeException(sprintf('User \'%s\' not found in the database. Api retrieval disabled.', $login));
            }
        }

        return $user;
    }

    /**
     * @param UserDTO $userData
     *
     * @return User
     *
     * @throws InvalidUserDataException
     */
    public function findOrCreateFromDTO(UserDTO $userData): User
    {
        // @todo LOG

        if (!$userData->isValid()) {
            throw new InvalidUserDataException('Invalid user data', $userData);
        }

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($userData->getId()))) {
            $user = new User(
                $userData->getId(),
                \DateTime::createFromFormat('Y-m-d_H:i:s', $userData->getCreated()) ?: new \DateTime()
            );
            $this->userRepository->add($user);
        }

        $user->updateLoginAndName($userData->getLogin(), $userData->getName());

        if (null !== $userData->getDenyAnonymous() && null !== $userData->getPrivate()) {
            $user->updatePrivacy(!$userData->getDenyAnonymous(), $userData->getPrivate());
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function findOrCreateFromDTOArray(array $usersData): array
    {
        // @todo LOG

        $result = [];

        foreach ($usersData as $userData) {
            $result[] = $this->findOrCreateFromDTO($userData);
        }

        return $result;
    }
}