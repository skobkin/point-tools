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
        if (null === ($user = $this->userRepository->find($userData->id))) {
            $user = new User(
                $userData->id,
                $userData->login,
                $userData->created,
            );
            $this->userRepository->save($user);
        }

        $user->updateLoginAndName($userData->login, $userData->name);

        if (null !== $userData->denyAnonymous && null !== $userData->private) {
            $user->updatePrivacy(!$userData->denyAnonymous, $userData->private);
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