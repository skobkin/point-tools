<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Comment;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class CommentFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $commentRepository;

    /**
     * @var EntityRepository
     */
    private $postRepository;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param EntityManagerInterface $em
     * @param UserFactory $userFactory
     */
    public function __construct(EntityManagerInterface $em, UserFactory $userFactory)
    {
        $this->em = $em;
        $this->userFactory = $userFactory;
        $this->commentRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Comment');
        $this->postRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Post');
    }

    /**
     * @param array $data
     *
     * @return Comment
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function createFromArray(array $data)
    {
        $this->validateData($data);

        if (null === ($comment = $this->commentRepository->find(['post' => $data['post_id'], 'id' => $data['id']]))) {
            // @fixme rare non-existing post bug
            $post = $this->postRepository->find($data['post_id']);
            $author = $this->userFactory->createFromArray($data['author']);
            if (null !== $data['to_comment_id']) {
                $toComment = $this->commentRepository->find(['post' => $data['post_id'], 'id' => $data['to_comment_id']]);
            } else {
                $toComment = null;
            }
            $createdAt = new \DateTime($data['created']);

            $comment = new Comment($data['id'], $post, $author, $toComment, $data['text'], $createdAt, $data['is_rec']);

            $this->em->persist($comment);
        }

        try {
            $this->em->flush($comment);
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Error while flushing changes for #%s/%d: %s', $data['post_id'], $data['id'], $e->getMessage()), 0, $e);
        }

        return $comment;
    }

    /**
     * @param array $data
     *
     * @return Comment[]
     * @throws ApiException
     */
    public function createFromListArray(array $data)
    {
        $comments = [];

        foreach ($data as $commentData) {
            $comments[] = $this->createFromArray($commentData);
        }

        return $comments;
    }

    /**
     * @param array $data
     *
     * @throws InvalidResponseException
     */
    private function validateData(array $data)
    {
        if (!array_key_exists('author', $data)) {
            throw new InvalidResponseException('Comment author data not found in API response');
        }

        // Post
        if (!(
            array_key_exists('id', $data) &&
            array_key_exists('is_rec', $data) &&
            array_key_exists('to_comment_id', $data) &&
            array_key_exists('post_id', $data) &&
            array_key_exists('text', $data) &&
            array_key_exists('created', $data)
            )) {
            throw new InvalidResponseException('Comment data not found in API response');
        }
    }
}