<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\MetaPost;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\PostsPage;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\PostTag;
use Skobkin\Bundle\PointToolsBundle\Exception\Factory\Blog\InvalidDataException;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\PostRepository;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\InvalidResponseException;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\AbstractFactory;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\UserFactory;

class PostFactory extends AbstractFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PostRepository
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


    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        PostRepository $postRepository,
        UserFactory $userFactory,
        FileFactory $fileFactory,
        CommentFactory $commentFactory,
        TagFactory $tagFactory
    ) {
        parent::__construct($logger);
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->userFactory = $userFactory;
        $this->fileFactory = $fileFactory;
        $this->commentFactory = $commentFactory;
        $this->tagFactory = $tagFactory;
    }

    /**
     * Creates posts and return status of new insertions
     *
     * @throws InvalidResponseException
     */
    public function createFromPageDTO(PostsPage $page): bool
    {
        $posts = [];

        $hasNew = false;

        foreach ((array) $page->getPosts() as $postData) {
            try {
                if (null === $this->postRepository->find($postData->getPost()->getId())) {
                    $hasNew = true;
                }

                $post = $this->createFromDTO($postData);
                $posts[] = $post;
            } catch (\Exception $e) {
                $this->logger->error('Error while processing post DTO', [
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
     * @todo Implement full post processing with comments
     *
     * @throws InvalidDataException
     */
    private function createFromDTO(MetaPost $postData): Post
    {
        if (!$postData->isValid()) {
            throw new InvalidDataException('Invalid post data');
        }

        try {
            $user = $this->userFactory->findOrCreateFromDTO($postData->getPost()->getAuthor());
        } catch (\Exception $e) {
            $this->logger->error('Error while creating user from DTO');
            throw $e;
        }

        if (null === ($post = $this->postRepository->find($postData->getPost()->getId()))) {
            // Creating new post
            $post = new Post($postData->getPost()->getId());
            $this->postRepository->add($post);
        }

        // Updating data
        $post
            ->setAuthor($user)
            ->setCreatedAt((new \DateTime($postData->getPost()->getCreated())) ?: null)
            ->setType($postData->getPost()->getType() ?: Post::TYPE_POST)
            ->setText($postData->getPost()->getText())
            ->setPrivate($postData->getPost()->getPrivate())
        ;

        try {
            $this->updatePostTags($post, $postData->getPost()->getTags() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post tags');
            throw $e;
        }

        try {
            $this->updatePostFiles($post, $postData->getPost()->getFiles() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post files');
            throw $e;
        }

        return $post;
    }

    /**
     * @param Post $post
     * @param string[] $tagsStrings
     */
    private function updatePostTags(Post $post, array $tagsStrings): void
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
    private function updatePostFiles(Post $post, array $urls): void
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
}