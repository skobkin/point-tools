<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Api;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\MetaPost;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\{NotFoundException, PostNotFoundException};
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;

/**
 * Basic Point.im user API functions from /api/post
 */
class PostApi extends AbstractApi
{
    private const PREFIX = '/api/post/';

    /**
     * @var PostFactory
     */
    private $postFactory;


    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer, LoggerInterface $logger, PostFactory $postFactory)
    {
        parent::__construct($httpClient, $serializer, $logger);

        $this->postFactory = $postFactory;
    }

    /**
     * @throws PostNotFoundException
     */
    public function getById(string $id): Post
    {
        try {
            $postData = $this->getGetJsonData(
                self::PREFIX.$id,
                [],
                MetaPost::class
            );
        } catch (NotFoundException $e) {
            throw new PostNotFoundException($id, $e);
        }

        // Not catching ForbiddenException right now

        return $this->postFactory->findOrCreateFromDtoWithContent($postData);
    }
}
