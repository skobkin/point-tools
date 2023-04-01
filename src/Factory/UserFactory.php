<?php
declare(strict_types=1);

namespace App\Factory;

use Psr\Log\LoggerInterface;
use App\DTO\Api\User as UserDTO;
use App\Entity\User;
use App\Exception\Factory\InvalidUserDataException;
use App\Repository\UserRepository;

class UserFactory extends AbstractFactory
{
    public const DATE_FORMAT = 'Y-m-d_H:i:s';

    public function __construct(
        LoggerInterface $logger,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($logger);
    }

    public function findOrCreateFromDTO(UserDTO $userData): User
    {
        // @todo LOG

        if (!$userData->isValid()) {
            throw new InvalidUserDataException($userData);
        }

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($userData->getId()))) {
            $user = new User(
                (int) $userData->getId(),
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

    /** @return User[] */
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