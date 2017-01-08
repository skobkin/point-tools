<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @todo Refactor DTO deserialization
 */
class AbstractApi
{
    /**
     * @var ClientInterface HTTP-client from Guzzle
     */
    protected $client;

    /**
     * @var string Authentication token for API
     */
    protected $authToken;

    /**
     * @var string CSRF-token for API
     */
    protected $csRfToken;


    public function __construct(ClientInterface $httpClient)
    {
        $this->client = $httpClient;
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of query parameters
     *
     * @return ResponseInterface
     */
    public function sendGetRequest(string $path, array $parameters = []): ResponseInterface
    {
        return $this->client->request('GET', $path, ['query' => $parameters]);
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of request data
     *
     * @return ResponseInterface
     */
    public function sendPostRequest(string $path, array $parameters = []): ResponseInterface
    {
        return $request = $this->client->request('POST', $path, ['form_params' => $parameters]);
    }

    /**
     * Make GET request and return data from response
     *
     * @param string $path Path template
     * @param array $parameters Parameters array used to fill path template
     * @param bool $decodeJsonResponse Decode JSON or return plaintext
     * @param bool $decodeJsonToObjects Decode JSON objects to PHP objects instead of arrays
     *
     * @return mixed
     */
    public function getGetRequestData($path, array $parameters = [], bool $decodeJsonResponse = false, bool $decodeJsonToObjects = false)
    {
        $response = $this->sendGetRequest($path, $parameters);

        return $this->processResponse($response, $decodeJsonResponse, $decodeJsonToObjects);
    }

    /**
     * Make POST request and return data from response
     *
     * @param string $path Path template
     * @param array $parameters Parameters array used to fill path template
     * @param bool $decodeJson Decode JSON or return plaintext
     * @param bool $decodeToObjects Decode JSON objects to PHP objects instead of arrays
     *
     * @return mixed
     */
    public function getPostRequestData($path, array $parameters = [], bool $decodeJson = false, bool $decodeToObjects = false)
    {
        $response = $this->sendPostRequest($path, $parameters);

        return $this->processResponse($response, $decodeJson, $decodeToObjects);
    }

    /**
     * Get HTTP client base URL
     *
     * @return string Base URL of client
     */
    public function getBaseUrl(): string
    {
        return (string) $this->client->getConfig('base_uri');
    }

    /**
     * @param ResponseInterface $response
     * @param bool $decodeJson
     * @param bool $decodeToObjects
     *
     * @return string|array|object
     */
    private function processResponse(ResponseInterface $response, bool $decodeJson = false, bool $decodeToObjects = false)
    {
        if ($decodeJson) {
            if ($decodeToObjects) {
                return json_decode($response->getBody());
            } else {
                return json_decode($response->getBody(), true);
            }
        } else {
            return $response->getBody();
        }
    }
}
