<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\{MetaPost, Post as ApiPost, PostsPage};
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message as WebsocketMessage;
use Skobkin\Bundle\PointToolsBundle\Entity\{Blogs\Post, Blogs\PostTag, User};
use Skobkin\Bundle\PointToolsBundle\Exception\{Api\InvalidResponseException, Factory\Blog\InvalidDataException};
use Skobkin\Bundle\PointToolsBundle\Repository\{Blogs\PostRepository, UserRepository};
use Skobkin\Bundle\PointToolsBundle\Service\Factory\{AbstractFactory, UserFactory};

class PostFactory extends AbstractFactory
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PostRepository */
    private $postRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserFactory */
    private $userFactory;

    /** @var FileFactory */
    private $fileFactory;

    /** @var CommentFactory */
    private $commentFactory;

    /** @var TagFactory */
    private $tagFactory;


    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        PostRepository $postRepository,
        UserRepository $userRepository,
        UserFactory $userFactory,
        FileFactory $fileFactory,
        CommentFactory $commentFactory,
        TagFactory $tagFactory
    ) {
        parent::__construct($logger);
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
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

                $post = $this->findOrCreateFromDtoWithContent($postData);
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
            // @todo probably refactor?
            if ($this->em->getUnitOfWork()->isScheduledForInsert($post)) {
                $hasNew = true;
            }
        }

        return $hasNew;
    }

    /**
     * Create full post with tags, files and comments
     *
     * @throws InvalidDataException
     */
    public function findOrCreateFromDtoWithContent(MetaPost $metaPost): Post
    {
        if (!$metaPost->isValid()) {
            throw new InvalidDataException('Invalid post data');
        }

        $postData = $metaPost->getPost();

        try {
            $author = $this->userFactory->findOrCreateFromDTO($metaPost->getPost()->getAuthor());
        } catch (\Exception $e) {
            $this->logger->error('Error while creating user from DTO');
            throw $e;
        }

        $post = $this->findOrCreateFromApiDto($postData, $author);

        try {
            $this->updatePostTags($post, $postData->getTags() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post tags');
            throw $e;
        }

        try {
            $this->updatePostFiles($post, $postData->getFiles() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post files');
            throw $e;
        }

        // @TODO implement comments

        return $post;
    }

    public function findOrCreateFromWebsocketDto(WebsocketMessage $message): Post
    {
        if (!$message->isValid()) {
            throw new InvalidDataException('Invalid post data');
        }
        if (!$message->isPost()) {
            throw new \LogicException(sprintf(
                'Incorrect message type received. \'post\' expected \'%s\' given',
                $message->getA()
            ));
        }

        if (null === $post = $this->postRepository->find($message->getPostId())) {
            $author = $this->userFactory->findOrCreateFromIdLoginAndName(
                $message->getAuthorId(),
                $message->getAuthor(),
                $message->getAuthorName()
            );

            $post = new Post(
                $message->getPostId(),
                $author,
                new \DateTime(),
                Post::TYPE_POST
            );
            $this->postRepository->add($post);
        }

        $post
            ->setText($message->getText())
            ->setPrivate((bool) $message->getPrivate())
        ;

        try {
            $this->updatePostTags($post, $message->getTags() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post tags');
            throw $e;
        }

        try {
            $this->updatePostFiles($post, $message->getFiles() ?: []);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating post files');
            throw $e;
        }

        return $post;
    }

    private function findOrCreateFromApiDto(ApiPost $postData, User $author): Post
    {
        if (null === ($post = $this->postRepository->find($postData->getId()))) {
            // Creating new post
            $post = new Post(
                $postData->getId(),
                $author,
                new \DateTime($postData->getCreated()),
                $postData->getType() ?: Post::TYPE_POST
            );
            $this->postRepository->add($post);
        }

        $post
            ->setText($postData->getText())
            ->setPrivate((bool) $postData->getPrivate())
        ;

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
                $tmpPostTag = new PostTag($post, $tag, $tagStringsHash[mb_strtolower($tag->getText())]);
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