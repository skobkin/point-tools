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
     * Make GET request and return response body
     */
    public function getGetResponseBody($path, array $parameters = []): StreamInterface
    {
        return $this->sendGetRequest($path, $parameters)->getBody();
    }

    /**
     * Make POST request and return response body
     */
    public function getPostResponseBody(string $path, array $parameters = []): StreamInterface
    {
        return $this->sendPostRequest($path, $parameters)->getBody();
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of query parameters
     *
     * @return ResponseInterface
     *
     * @throws NetworkException
     */
    private function sendGetRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending GET request', ['path' => $path, 'parameters' => $parameters]);

        return $this->sendRequest('GET', $path, ['query' => $parameters]);
    }

    /**
     * @param string $path Request path
     * @param array $parameters Key => Value array of request data
     *
     * @return ResponseInterface
     *
     * @throws NetworkException
     */
    private function sendPostRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending POST request', ['path' => $path, 'parameters' => $parameters]);

        return $this->sendRequest('POST', $path, ['form_params' => $parameters]);
    }

    private function sendRequest(string $method, string $path, array $parameters): ResponseInterface
    {
        try {
            $response = $this->client->request($method, $path, ['query' => $parameters]);

            $this->checkResponse($response);

            return $response;
        } catch (TransferException $e) {
            $this->processTransferException($e);
            
            throw new NetworkException('Request error', $e->getCode(), $e);
        }
    }

    /**
     * @todo refactor with $this->checkResponse()
     *
     * @param \Exception $e
     *
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws ServerProblemException
     * @throws UnauthorizedException
     */
    private function processTransferException(\Exception $e): void
    {
        switch ($e->getCode()) {
            case SymfonyResponse::HTTP_UNAUTHORIZED:
                throw new UnauthorizedException('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED, $e);
            case SymfonyResponse::HTTP_NOT_FOUND:
                throw new NotFoundException('Resource not found', SymfonyResponse::HTTP_NOT_FOUND, $e);
            case SymfonyResponse::HTTP_FORBIDDEN:
                throw new ForbiddenException('Forbidden', SymfonyResponse::HTTP_FORBIDDEN, $e);
            case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
            case SymfonyResponse::HTTP_NOT_IMPLEMENTED:
            case SymfonyResponse::HTTP_BAD_GATEWAY:
            case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
            case SymfonyResponse::HTTP_GATEWAY_TIMEOUT:
                throw new ServerProblemException('Server error', SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
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

        // @todo remove after fix
        // Temporary fix until @arts fixes this bug
        if ('{"error": "UserNotFound"}' === (string) $response->getBody()) {
            throw new NotFoundException($reason, $code);
        }

        switch ($code) {
            case SymfonyResponse::HTTP_UNAUTHORIZED:
                throw new UnauthorizedException($reason, $code);
            case SymfonyResponse::HTTP_FORBIDDEN:
                throw new ForbiddenException($reason, $code);
            case SymfonyResponse::HTTP_NOT_FOUND:
                throw new NotFoundException($reason, $code);
            case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
            case SymfonyResponse::HTTP_NOT_IMPLEMENTED:
            case SymfonyResponse::HTTP_BAD_GATEWAY:
            case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
            case SymfonyResponse::HTTP_GATEWAY_TIMEOUT:
                throw new ServerProblemException($reason, $code);
        }
    }
}
