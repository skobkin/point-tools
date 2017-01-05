<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Guzzle\Service\Client;
use Guzzle\Http\Message\Request as GuzzleRequest;
use Guzzle\Http\Message\Response as GuzzleResponse;

/**
 * @todo Refactor to Guzzle and DTO
 * @see https://github.com/misd-service-development/guzzle-bundle/blob/master/Resources/doc/serialization.md
 * @see https://github.com/misd-service-development/guzzle-bundle/blob/master/Resources/doc/clients.md
 * @see https://github.com/misd-service-development/guzzle-bundle/blob/master/Resources/doc/param_converter.md
 */
class AbstractApi
{
    /**
     * @var Client HTTP-client from Guzzle
     */
    protected $client;

    /**
     * @var bool Use HTTPS instead of HTTP
     */
    protected $useHttps;

    /**
     * @var string Authentication token for API
     */
    protected $authToken;

    /**
     * @var string CSRF-token for API
     */
    protected $csRfToken;

    /**
     * @param Client $httpClient HTTP-client from Guzzle
     * @param bool $https Use HTTPS instead of HTTP
     * @param string $baseUrl Base URL for API
     */
    public function __construct(Client $httpClient, $https = true, $baseUrl = null)
    {
        $this->client = $httpClient;
        $this->useHttps = ($https) ? true : false;

        if (null !== $baseUrl) {
            $this->setBaseUrl($baseUrl);
        }
    }

    /**
     * Make GET request and return Response object
     *
     * @param string $path Request path
     * @param array $parameters Key => Value array of query parameters
     * @return GuzzleResponse
     */
    public function sendGetRequest($path, array $parameters = [])
    {
        /** @var GuzzleRequest $request */
        $request = $this->client->get($path);

        $query = $request->getQuery();

        foreach ($parameters as $parameter => $value) {
            $query->set($parameter, $value);
        }

        return $request->send();
    }

    /**
     * Make POST request and return Response object
     *
     * @param string $path Request path
     * @param array $parameters Key => Value array of request data
     * @return GuzzleResponse
     */
    public function sendPostRequest($path, array $parameters = [])
    {
        /** @var GuzzleRequest $request */
        $request = $this->client->post($path, null, $parameters);

        return $request->send();
    }

    /**
     * Make GET request and return data from response
     *
     * @param string $path Path template
     * @param array $parameters Parameters array used to fill path template
     * @param bool $decodeJsonResponse Decode JSON or return plaintext
     * @param bool $decodeJsonToObjects Decode JSON objects to PHP objects instead of arrays
     * @return mixed
     */
    public function getGetRequestData($path, array $parameters = [], $decodeJsonResponse = false, $decodeJsonToObjects = false)
    {
        $response = $this->sendGetRequest($path, $parameters);

        if ($decodeJsonResponse) {
            if ($decodeJsonToObjects) {
                return json_decode($response->getBody(true));
            } else {
                return $response->json();
            }
        } else {
            return $response->getBody(true);
        }
    }

    /**
     * Make POST request and return data from response
     *
     * @param string $path Path template
     * @param array $parameters Parameters array used to fill path template
     * @param bool $decodeJsonResponse Decode JSON or return plaintext
     * @param bool $decodeJsonToObjects Decode JSON objects to PHP objects instead of arrays
     * @return mixed
     */
    public function getPostRequestData($path, array $parameters = [], $decodeJsonResponse = false, $decodeJsonToObjects = false)
    {
        $response = $this->sendPostRequest($path, $parameters);

        if ($decodeJsonResponse) {
            if ($decodeJsonToObjects) {
                return json_decode($response->getBody(true));
            } else {
                return $response->json();
            }
        } else {
            return $response->getBody(true);
        }
    }

    /**
     * Get HTTP client base URL
     *
     * @return string Base URL of client
     */
    public function getBaseUrl()
    {
        return $this->client->getBaseUrl();
    }

    /**
     * Set HTTP client base URL
     *
     * @param string $baseUrl Base URL of API
     * @param bool $useProtocol Do not change URL scheme (http/https) defined in $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl, $useProtocol = false)
    {
        // Overriding protocol
        if (!$useProtocol) {
            $baseUrl = str_replace(['http://', 'https://',], ($this->useHttps) ? 'https://' : 'http://', $baseUrl);
        }
        // Adding missing protocol
        if ((false === strpos(strtolower($baseUrl), 'http://')) && (false === strpos(strtolower($baseUrl), 'https://'))) {
            $baseUrl = (($this->useHttps) ? 'https://' : 'http://') . $baseUrl;
        }

        $this->client->setBaseUrl($baseUrl);

        return $this;
    }

    /**
     * Check if API service uses HTTPS
     *
     * @return bool
     */
    public function isHttps()
    {
        return $this->useHttps;
    }

    /**
     * Enable HTTPS
     *
     * @return $this
     */
    public function enableHttps()
    {
        $this->useHttps = true;
        $this->setBaseUrl($this->getBaseUrl());

        return $this;
    }

    /**
     * Disable HTTPS
     *
     * @return $this
     */
    public function disableHttps()
    {
        $this->useHttps = false;
        $this->setBaseUrl($this->getBaseUrl());

        return $this;
    }
}
