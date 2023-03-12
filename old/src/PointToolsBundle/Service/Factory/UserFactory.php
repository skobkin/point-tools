<?php

namespace src\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;
use src\PointToolsBundle\DTO\Api\User as UserDTO;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Repository\UserRepository;
use src\PointToolsBundle\Exception\Factory\InvalidUserDataException;
use src\PointToolsBundle\Service\Factory\AbstractFactory;

class UserFactory extends AbstractFactory
{
    public const DATE_FORMAT = 'Y-m-d_H:i:s';

    /** @var UserRepository */
    private $userRepository;


    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
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