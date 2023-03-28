<?php
declare(strict_types=1);

namespace App\Service\Api;

use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use App\Factory\Blog\PostFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** Basic Point.im user API functions from /api/post */
class PostApi extends AbstractApi
{
    public function __construct(
        HttpClientInterface $pointApiClient,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        private readonly PostFactory $postFactory,
    ) {
        parent::__construct($pointApiClient, $logger, $serializer);
    }
}
