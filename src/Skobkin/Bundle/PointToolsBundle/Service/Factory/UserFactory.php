<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\User as UserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Exception\Factory\InvalidUserDataException;

class UserFactory extends AbstractFactory
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
    }

    /**
     * @throws InvalidUserDataException
     */
    public function findOrCreateFromDTO(UserDTO $userData): User
    {
        if (!$userData->isValid()) {
            throw new InvalidUserDataException('Invalid user data', $userData);
        }

        $createdAt = \DateTime::createFromFormat(self::DATE_FORMAT, $userData->getCreated()) ?: new \DateTime();

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($userData->getId()))) {
            $user = new User(
                $userData->getId(),
                $createdAt
            );
            $this->userRepository->add($user);
        } else {
            $user->updateCreatedAt($createdAt);
        }

        $user->updateLoginAndName($userData->getLogin(), $userData->getName());

        if (null !== $userData->getDenyAnonymous() && null !== $userData->getPrivate()) {
            $user->updatePrivacy(!$userData->getDenyAnonymous(), $userData->getPrivate());
        }

        return $user;
    }

    public function findOrCreateFromIdLoginAndName(int $id, string $login, ?string $name): User
    {
        /** @var User $user */
        if (null === $user = $this->userRepository->find($id)) {
            // We're using current date now but next time when we'll be updating user from API it'll be fixed
            $user = new User($id, new \DateTime(), $login, $name);
            $this->userRepository->add($user);
        } else {
            // @todo update login?
            // Probably don't because no name in the WS message (or maybe after PR to point?)
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