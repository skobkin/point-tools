<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\MetaPost;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler\PostsPage;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\PostTag;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\Factory\Blogs\InvalidPostDataException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\UserFactory;


class PostFactory
{
    /**
     * @var LoggerInterface
     */
    private $log;

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
     * @var FileFactory
     */
    private $fileFactory;

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
    public function __construct(LoggerInterface $log, EntityManager $em, UserFactory $userFactory, FileFactory $fileFactory, CommentFactory $commentFactory, TagFactory $tagFactory)
    {
        $this->log = $log;
        $this->userFactory = $userFactory;
        $this->fileFactory = $fileFactory;
        $this->commentFactory = $commentFactory;
        $this->tagFactory = $tagFactory;
        $this->em = $em;
        $this->postRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Post');
    }

    /**
     * Creates posts and return status of new insertions
     *
     * @param PostsPage $page
     *
     * @return bool
     *
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function createFromPageDTO(PostsPage $page)
    {
        $posts = [];

        $hasNew = false;

        foreach ($page->getPosts() as $postData) {
            try {
                if (null === $this->postRepository->find($postData->getPost()->getId())) {
                    $hasNew = true;
                }

                $post = $this->createFromDTO($postData);
                $posts[] = $post;
            } catch (\Exception $e) {
                $this->log->error('Error while processing post DTO', [
                    'post_id' => $postData->getPost()->getId(),
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }

        foreach ($posts as $post) {
            if ($this->em->getUnitOfWork()->isScheduledForInsert($post)) {
                $hasNew = true;
            }
        }

        return $hasNew;
    }

    /**
     * @todo Implement full post with comments processing
     *
     * @param MetaPost $postData
     *
     * @return Post
     * @throws ApiException
     * @throws InvalidPostDataException
     */
    private function createFromDTO(MetaPost $postData)
    {
        if (!$this->validateMetaPost($postData)) {
            throw new InvalidPostDataException('Invalid post data', $postData);
        }

        if (!$postData->getPost()->getAuthor()->getId()) {
            $this->log->error('Post author does not contain id', ['post_id' => $postData->getPost()->getId()]);
            throw new InvalidPostDataException('Post author does not contain id', $postData);
        }

        try {
            $user = $this->userFactory->createFromDTO($postData->getPost()->getAuthor());
        } catch (\Exception $e) {
            $this->log->error('Error while creating user from DTO');
            throw $e;
        }

        if (null === ($post = $this->postRepository->find($postData->getPost()->getId()))) {
            // Creating new post
            $post = new Post($postData->getPost()->getId());
            $this->em->persist($post);
        }

        // Updating data
        $post
            ->setAuthor($user)
            ->setCreatedAt((new \DateTime($postData->getPost()->getCreated())) ?: null)
            // @fixme Need bugfix for point API (type is not showing now)
            //->setType($postData->getPost()->getType())
            ->setText($postData->getPost()->getText())
            ->setPrivate($postData->getPost()->getPrivate())
        ;

        try {
            $this->updatePostTags($post, $postData->getPost()->getTags() ?: []);
        } catch (\Exception $e) {
            $this->log->error('Error while updating post tags');
            throw $e;
        }

        try {
            $this->updatePostFiles($post, $postData->getPost()->getFiles() ?: []);
        } catch (\Exception $e) {
            $this->log->error('Error while updating post files');
            throw $e;
        }

        return $post;
    }

    /**
     * @param Post $post
     * @param string[] $tagsStrings
     */
    private function updatePostTags(Post $post, array $tagsStrings)
    {
        $tags = $this->tagFactory->createFromStringsArray($tagsStrings);

        // Hashing tags strings
        $tagStringsHash = [];
        foreach ($tagsStrings as $tagsString) {
            $tagStringsHash[mb_strtolower($tagsString)] = $tagsString;
        }

        // Hashing current post tags
        $newTagsHash = [];
        foreach ($tags as $tag) {
            $newTagsHash[mb_strtolower($tag->getText())] = $tag;
        }

        // Hashing old post tags (from DB)
        $oldTagsHash = [];
        foreach ($post->getPostTags() as $postTag) {
            $oldTagsHash[mb_strtolower($postTag->getOriginalTagText())] = $postTag;
        }

        // Adding missing tags
        foreach ($tags as $tag) {
            if (!array_key_exists(mb_strtolower($tag->getText()), $oldTagsHash)) {
                $tmpPostTag = (new PostTag($tag))
                    ->setText($tagStringsHash[mb_strtolower($tag->getText())])
                ;
                $post->addPostTag($tmpPostTag);
            }
        }

        // Removing deleted tags
        foreach ($post->getPostTags() as $postTag) {
            if (!array_key_exists(mb_strtolower($postTag->getOriginalTagText()), $newTagsHash)) {
                $post->removePostTag($postTag);
            }
        }
    }

    /**
     * @param Post $post
     * @param array $urls
     */
    private function updatePostFiles(Post $post, array $urls)
    {
        $files = $this->fileFactory->createFromUrlsArray($urls);

        // Adding missing files
        foreach ($files as $file) {
            if (!$post->getFiles()->contains($file)) {
                $post->addFile($file);
            }
        }

        // Removing deleted files
        foreach ($post->getFiles() as $file) {
            if (!in_array($file, $files, true)) {
                $post->removeFile($file);
            }
        }
    }

    private function validateMetaPost(MetaPost $post)
    {
        if (!$post->getPost()->getId()) {
            $this->log->error('Post DTO contains no id');
            return false;
        }

        if (null === $post->getPost()->getAuthor()->getId()) {
            $this->log->error('Post DTO contains no valid User DTO.', ['post_id' => $post->getPost()->getId()]);
            return false;
        }

        return true;
    }
}