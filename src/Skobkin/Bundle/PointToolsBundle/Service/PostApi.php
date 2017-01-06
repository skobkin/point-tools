<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Guzzle\Service\Client;
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


    public function __construct(Client $httpClient, $https = true, $baseUrl = null, PostFactory $postFactory)
    {
        parent::__construct($httpClient, $https, $baseUrl);

        $this->postFactory = $postFactory;
    }
}
