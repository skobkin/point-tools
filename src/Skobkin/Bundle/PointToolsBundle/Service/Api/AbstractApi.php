<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\ForbiddenException;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\NetworkException;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\NotFoundException;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\ServerProblemException;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AbstractApi
{
    /**
     * @var ClientInterface HTTP-client from Guzzle
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string Authentication token for API
     */
    protected $authToken;

    /**
     * @var string CSRF-token for API
     */
    protected $csRfToken;


    public function __construct(ClientInterface $httpClient, Serializer $serializer, LoggerInterface $logger)
    {
        $this->client = $httpClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of query parameters
     *
     * @return ResponseInterface
     *
     * @throws NetworkException
     */
    public function sendGetRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending GET request', ['path' => $path, 'parameters' => $parameters]);

        try {
            return $this->client->request('GET', $path, ['query' => $parameters]);
        } catch (TransferException $e) {
            throw new NetworkException('Request error', $e->getCode(), $e);
        }
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of request data
     *
     * @return ResponseInterface
     *
     * @throws NetworkException
     */
    public function sendPostRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending POST request', ['path' => $path, 'parameters' => $parameters]);

        try {
            return $this->client->request('POST', $path, ['form_params' => $parameters]);
        } catch (TransferException $e) {
            throw new NetworkException('Request error', $e->getCode(), $e);
        }
    }

    /**
     * Make GET request and return response body
     */
    public function getGetResponseBody($path, array $parameters = []): StreamInterface
    {
        $response = $this->sendGetRequest($path, $parameters);

        $this->checkResponse($response);

        return $response->getBody();
    }

    /**
     * Make POST request and return response body
     */
    public function getPostResponseBody(string $path, array $parameters = []): StreamInterface
    {
        $response = $this->sendPostRequest($path, $parameters);

        $this->checkResponse($response);

        return $response->getBody();
    }

    /**
     * Make GET request and return DTO objects
     *
     * @return array|object
     */
    public function getGetJsonData(string $path, array $parameters = [], string $type, DeserializationContext $context = null)
    {
        return $this->serializer->deserialize(
            $this->getGetResponseBody($path, $parameters),
            $type,
            'json',
            $context
        );
    }

    /**
     * Make POST request and return DTO objects
     *
     * @return array|object
     */
    public function getPostJsonData(string $path, array $parameters = [], string $type, DeserializationContext $context = null)
    {
        return $this->serializer->deserialize(
            $this->getPostResponseBody($path, $parameters),
            $type,
            'json',
            $context
        );
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws ServerProblemException
     * @throws UnauthorizedException
     */
    private function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        switch ($code) {
            case SymfonyResponse::HTTP_UNAUTHORIZED:
                throw new UnauthorizedException($reason, $code);
                break;
            case SymfonyResponse::HTTP_FORBIDDEN:
                throw new ForbiddenException($reason, $code);
                break;
            case SymfonyResponse::HTTP_NOT_FOUND:
                throw new NotFoundException($reason, $code);
                break;
            case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
            case SymfonyResponse::HTTP_NOT_IMPLEMENTED:
            case SymfonyResponse::HTTP_BAD_GATEWAY:
            case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
            case SymfonyResponse::HTTP_GATEWAY_TIMEOUT:
                throw new ServerProblemException($reason, $code);
                break;
        }
    }
}
