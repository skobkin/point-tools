<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Guzzle\Service\Client;
use Guzzle\Http\Message\Request as GuzzleRequest;
use Guzzle\Http\Message\Response as GuzzleResponse;

// @todo Implement commands: https://github.com/misd-service-development/guzzle-bundle/blob/master/Resources/doc/serialization.md
class AbstractApi
{
    /**
     * @var Client HTTP-client from Guzzle
     */
    protected $client;

    public function __construct($httpClient)
    {
        $this->client = $httpClient;
    }

    /**
     * Make GET request and return Response object
     *
     * @param string $pathTemplate
     * @param array $parameters
     * @return GuzzleResponse
     */
    public function sendGetRequest($pathTemplate, array $parameters = [])
    {
        $path = vsprintf($pathTemplate, $parameters);

        $request = $this->client->get($path);

        return $request->send();
    }
}
