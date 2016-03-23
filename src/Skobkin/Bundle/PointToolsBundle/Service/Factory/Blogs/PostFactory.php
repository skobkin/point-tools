<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\UserFactory;


class PostFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $postRepository;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var CommentFactory
     */
    private $commentFactory;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em, UserFactory $userFactory, CommentFactory $commentFactory, TagFactory $tagFactory)
    {
        $this->userFactory = $userFactory;
        $this->commentFactory = $commentFactory;
        $this->tagFactory = $tagFactory;
        $this->em = $em;
        $this->postRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Post');
    }

    /**
     * @param array $data
     *
     * @return Post
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function createFromArray(array $data)
    {
        $this->validateData($data);

        if (null === ($post = $this->postRepository->find($data['post']['id']))) {
            $createdAt = new \DateTime($data['post']['created']);
            $author = $this->userFactory->createFromArray($data['post']['author']);

            $post = new Post($data['post']['id'], $data['post']['type'], $data['post']['text'], $createdAt, $author);
            $this->em->persist($post);
        }

        $post->setText($data['post']['text']);

        // Tags
        $tags = $this->tagFactory->createFromListArray($data['post']['tags']);

        // Removing deleted tags
        foreach ($post->getTags() as $tag) {
            if (false === in_array($tag, $tags, true)) {
                $post->removeTag($tag);
            }
        }

        // Adding new tags
        foreach ($tags as $tag) {
            if (!$post->getTags()->contains($tag)) {
                $post->addTag($tag);
            }
        }

        // Flushing post before linking comments
        try {
            $this->em->flush($post);
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Error while flushing changes for #%s: %s', $data['post']['id'], $e->getMessage()), 0, $e);
        }

        // Comments
        $comments = $this->commentFactory->createFromListArray($data['comments']);

        // Marking removed comments
        foreach ($post->getComments() as $comment) {
            if (false === in_array($comment, $comments, true)) {
                $comment->setDeleted(true);
            }
        }

        // Adding comments
        foreach ($comments as $comment) {
            $post->addComment($comment);
        }

        return $post;
    }

    /**
     * @param array $data
     *
     * @throws InvalidResponseException
     */
    private function validateData(array $data)
    {
        if (!array_key_exists('post', $data)) {
            throw new InvalidResponseException('Post data not found in API response');
        }

        if (!array_key_exists('comments', $data)) {
            throw new InvalidResponseException('Comments data not found in API response');
        }

        if (!(
            array_key_exists('id', $data['post']) &&
            array_key_exists('type', $data['post']) &&
            array_key_exists('text', $data['post']) &&
            array_key_exists('tags', $data['post']) &&
            array_key_exists('author', $data['post'])
            )) {
            throw new InvalidResponseException('Post content not found in API response');
        }
    }
}