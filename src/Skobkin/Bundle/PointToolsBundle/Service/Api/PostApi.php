<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Api;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs\PostFactory;

/**
 * Basic Point.im user API functions from /api/post
 */
class PostApi extends AbstractApi
{
    /**
     * @var PostFactory
     */
    private $postFactory;


    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer, LoggerInterface $logger, PostFactory $postFactory)
    {
        parent::__construct($httpClient, $serializer, $logger);

        $this->postFactory = $postFactory;
    }
}
