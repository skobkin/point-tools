<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Guzzle\Service\Client;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class PostApi extends AbstractApi
{
    const PATH_ALL_POSTS = '/api/all';

    /**
     * @var EntityManager
     */
    protected $em;


    public function __construct(Client $httpClient, $https = true, $baseUrl = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($httpClient, $https, $baseUrl);

        $this->em = $entityManager;
    }

    public function getName()
    {
        return 'skobkin_point_tools_api_post';
    }

    /**
     * Get user subscribers by user id
     *
     * @param int $id
     * @return User[]
     */
    public function getUserSubscribersById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $usersList = $this->getGetRequestData('/api/user/id/' . (int) $id . '/subscribers', [], true);

        $users = $this->getUsersFromList($usersList);

        return $users;
    }

    /**
     * @param array $users
     * @return User[]
     */
    private function getUsersFromList(array $users = [])
    {
        /** @var EntityRepository $userRepo */
        $userRepo = $this->em->getRepository('SkobkinPointToolsBundle:User');

        $resultUsers = [];

        foreach ($users as $userData) {
            if (array_key_exists('id', $userData) && array_key_exists('login', $userData) && array_key_exists('name', $userData) && is_numeric($userData['id'])) {

                // @todo Optimize with prehashed id's list
                $user = $userRepo->findOneBy(['id' => $userData['id']]);

                if (!$user) {
                    $user = new User();
                    $user->setId((int) $userData['id']);
                    $this->em->persist($user);
                }

                // Updating data
                if ($user->getLogin() !== $userData['login']) {
                    $user->setLogin($userData['login']);
                }
                if ($user->getName() !== $userData['name']) {
                    $user->setName($userData['name']);
                }

                $resultUsers[] = $user;
            }
        }

        $this->em->flush();

        return $resultUsers;
    }

    /**
     * @param array $posts
     */
    private function getPostsFromList(array $posts = [])
    {
        /** @var EntityRepository $postRepo */
        $postRepo = $this->em->getRepository('SkobkinPointToolsBundle:Blogs\Post');

        $resultUsers = [];

        foreach ($posts as $postData) {
            if (array_key_exists('id', $postData) && array_key_exists('uid', $postData) && array_key_exists('post', $postData)) {

                // @todo Optimize with prehashed id's list
                $post = $postRepo->findOneBy(['id' => $postData['id']]);

                if (!$post) {
                    $post = new Post();
                    $post
                        ->setId($postData['id'])
                        ->setText($postData['post']['text'])
                        //->setCreatedAt()
                        //->setAuthor()
                        ->setType($postData['post']['type'])
                    ;
                    $this->em->persist($post);
                }

                // Updating data
                if ($post->getLogin() !== $postData['login']) {
                    $post->setLogin($postData['login']);
                }
                if ($post->getName() !== $postData['name']) {
                    $post->setName($postData['name']);
                }

                $resultUsers[] = $post;
            }
        }

        $this->em->flush();

        return $resultUsers;
    }
}
