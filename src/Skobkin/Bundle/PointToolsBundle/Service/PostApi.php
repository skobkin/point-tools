<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Guzzle\Service\Client;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Post;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\PostFactory;

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

    public function getName()
    {
        return 'skobkin_point_tools_api_post';
    }

    /**
     * Get post with tags and comments by id
     *
     * @param $id
     *
     * @return Post[]
     */
    public function getPostById($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException('$id must be an string');
        }

        $postData = $this->getGetRequestData('/api/post/'.$id, [], true);

        $post = $this->postFactory->createFromArray($postData);

        return $post;
    }
}
