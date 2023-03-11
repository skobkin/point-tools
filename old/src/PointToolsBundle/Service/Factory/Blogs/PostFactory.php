<?php

namespace src\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use src\PointToolsBundle\DTO\Api\{PostsPage};
use src\PointToolsBundle\Entity\{Blogs\PostTag};
use src\PointToolsBundle\Exception\{Api\InvalidResponseException};
use src\PointToolsBundle\Repository\Blogs\PostRepository;
use src\PointToolsBundle\Service\Factory\{AbstractFactory, Blogs\CommentFactory, Blogs\TagFactory};
use src\PointToolsBundle\DTO\Api\MetaPost;
use src\PointToolsBundle\DTO\Api\Post as PostDTO;
use src\PointToolsBundle\Entity\Blogs\Post;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Exception\Factory\Blog\InvalidDataException;
use src\PointToolsBundle\Service\Factory\Blogs\FileFactory;
use src\PointToolsBundle\Service\Factory\UserFactory;
use function Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\mb_strtolower;

class PostFactory extends AbstractFactory
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PostRepository */
    private $postRepository;

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
     * @todo Implement comments
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

        $post = $this->findOrCreateFromDto($postData, $author);

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

        return $post;
    }

    private function findOrCreateFromDto(PostDTO $postData, User $author): Post
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
            ->setPrivate($postData->getPrivate())
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