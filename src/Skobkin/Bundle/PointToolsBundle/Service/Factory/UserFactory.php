<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
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
    public function __construct(EntityManagerInterface $em)
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
        $this->validateData($data);

        // @todo Return ID existance check when @ap-Codkelden will fix this API behaviour
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
     * @param array $data
     *
     * @throws InvalidResponseException
     */
    private function validateData(array $data)
    {
        if (!(array_key_exists('id', $data) && array_key_exists('login', $data) && array_key_exists('name', $data))) {
            throw new InvalidResponseException('Invalid user data');
        }
    }
}