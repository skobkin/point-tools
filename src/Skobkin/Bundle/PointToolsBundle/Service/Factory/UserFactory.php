<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\User as UserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Exception\Factory\InvalidUserDataException;

class UserFactory extends AbstractFactory
{
    /**
     * @var UserRepository
     */
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
            // Creating new user
            $user = new User($userData->getId());
            $this->userRepository->add($user);
        }

        // Updating data
        $user
            ->setLogin($userData->getLogin())
            ->setName($userData->getName())
        ;

        return $user;
    }

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