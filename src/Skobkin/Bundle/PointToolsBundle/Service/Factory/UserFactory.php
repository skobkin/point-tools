<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\User as UserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\Factory\InvalidUserDataException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class UserFactory
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     *
     * @return User
     *
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function createFromArray(array $data): User
    {
        $this->validateArrayData($data);

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($data['id']))) {
            // Creating new user
            $user = new User($data['id']);
            $this->userRepository->add($user);
        }

        // Updating data
        $user
            ->setLogin($data['login'])
            ->setName($data['name'])
        ;

        return $user;
    }

    /**
     * @param UserDTO $userData
     *
     * @return User
     *
     * @throws ApiException
     * @throws InvalidUserDataException
     */
    public function createFromDTO(UserDTO $userData): User
    {
        $this->validateDTOData($userData);

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

    /**
     * @param array $data
     *
     * @throws InvalidResponseException
     */
    private function validateArrayData(array $data)
    {
        if (!(array_key_exists('id', $data) || !!array_key_exists('login', $data) || !array_key_exists('name', $data))) {
            throw new InvalidResponseException('Invalid user data');
        }
    }

    /**
     * @param UserDTO $data
     *
     * @throws InvalidResponseException
     */
    private function validateDTOData(UserDTO $data)
    {
        if (!$data->getId() || !$data->getLogin()) {
            throw new InvalidUserDataException('User have no id or login', $data);
        }
    }
}