<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use GuzzleHttp\ClientInterface;
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


    public function __construct(ClientInterface $httpClient, PostFactory $postFactory)
    {
        parent::__construct($httpClient);

        $this->postFactory = $postFactory;
    }
}
