<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\User as UserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\Factory\InvalidUserDataException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class UserFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->userRepository = $em->getRepository('SkobkinPointToolsBundle:User');
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function createFromArray(array $data)
    {
        $this->validateArrayData($data);

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($data['id']))) {
            // Creating new user
            $user = new User($data['id']);
            $this->em->persist($user);
        }

        // Updating data
        $user
            ->setLogin($data['login'])
            ->setName($data['name'])
        ;

        try {
            $this->em->flush($user);
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Error while flushing changes for [%d] %s: %s', $user->getId(), $user->getLogin(), $e->getMessage()), 0, $e);
        }

        return $user;
    }

    /**
     * @param UserDTO $userData
     *
     * @return User
     * @throws ApiException
     * @throws InvalidUserDataException
     */
    public function createFromDTO(UserDTO $userData)
    {
        $this->validateDTOData($userData);

        /** @var User $user */
        if (null === ($user = $this->userRepository->find($userData->getId()))) {
            // Creating new user
            $user = new User($userData->getId());
            $this->em->persist($user);
        }

        // Updating data
        $user
            ->setLogin($userData->getLogin())
            ->setName($userData->getName())
        ;

        try {
            $this->em->flush($user);
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Error while flushing changes for [%d] %s: %s', $user->getId(), $user->getLogin(), $e->getMessage()), 0, $e);
        }

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
     * @param array $data
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