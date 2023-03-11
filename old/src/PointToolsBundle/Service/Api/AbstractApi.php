<?php

namespace src\PointToolsBundle\Service\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use JMS\Serializer\{DeserializationContext, SerializerInterface};
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Exception\Api\{
    src\PointToolsBundle\Exception\Api\ForbiddenException, src\PointToolsBundle\Exception\Api\NetworkException, src\PointToolsBundle\Exception\Api\NotFoundException, src\PointToolsBundle\Exception\Api\ServerProblemException, src\PointToolsBundle\Exception\Api\UnauthorizedException};
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

abstract class AbstractApi
{
    /**
     * @var ClientInterface HTTP-client from Guzzle
     */
    protected $client;

    /**
     * @var SerializerInterface
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


    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer, LoggerInterface $logger)
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
     * @throws \src\PointToolsBundle\Exception\Api\NetworkException
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
     * @throws \src\PointToolsBundle\Exception\Api\NetworkException
     */
    private function sendPostRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending POST request', ['path' => $path, 'parameters' => $parameters]);

        return $this->sendRequest('POST', $path, ['form_params' => $parameters]);
    }

    private function sendRequest(string $method, string $path, array $parameters): ResponseInterface
    {
        try {
            $response = $this->client->request($method, $path, $parameters);

            $this->checkResponse($response);

            return $response;
        } catch (TransferException $e) {
            $this->processTransferException($e);
            
            throw new \src\PointToolsBundle\Exception\Api\NetworkException('Request error', $e->getCode(), $e);
        }
    }

    /**
     * @param \Exception $e
     *
     * @throws \src\PointToolsBundle\Exception\Api\ForbiddenException
     * @throws \src\PointToolsBundle\Exception\Api\NotFoundException
     * @throws \src\PointToolsBundle\Exception\Api\ServerProblemException
     * @throws \src\PointToolsBundle\Exception\Api\UnauthorizedException
     * @todo refactor with $this->checkResponse()
     *
     */
    private function processTransferException(\Exception $e): void
    {
        switch ($e->getCode()) {
            case SymfonyResponse::HTTP_UNAUTHORIZED:
                throw new \src\PointToolsBundle\Exception\Api\UnauthorizedException('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED, $e);
            case SymfonyResponse::HTTP_NOT_FOUND:
                throw new \src\PointToolsBundle\Exception\Api\NotFoundException('Resource not found', SymfonyResponse::HTTP_NOT_FOUND, $e);
            case SymfonyResponse::HTTP_FORBIDDEN:
                throw new \src\PointToolsBundle\Exception\Api\ForbiddenException('Forbidden', SymfonyResponse::HTTP_FORBIDDEN, $e);
            case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
            case SymfonyResponse::HTTP_NOT_IMPLEMENTED:
            case SymfonyResponse::HTTP_BAD_GATEWAY:
            case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
            case SymfonyResponse::HTTP_GATEWAY_TIMEOUT:
                throw new \src\PointToolsBundle\Exception\Api\ServerProblemException('Server error', SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
    
    /**
     * @throws \src\PointToolsBundle\Exception\Api\ForbiddenException
     * @throws \src\PointToolsBundle\Exception\Api\NotFoundException
     * @throws \src\PointToolsBundle\Exception\Api\ServerProblemException
     * @throws \src\PointToolsBundle\Exception\Api\UnauthorizedException
     */
    private function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        // @todo remove after fix
        // Temporary fix until @arts fixes this bug
        if ('{"error": "UserNotFound"}' === (string) $response->getBody()) {
            throw new \src\PointToolsBundle\Exception\Api\NotFoundException('Not found', SymfonyResponse::HTTP_NOT_FOUND);
        } elseif ('{"message": "Forbidden", "code": 403, "error": "Forbidden"}' === (string) $response->getBody()) {
            throw new \src\PointToolsBundle\Exception\Api\ForbiddenException('Forbidden', SymfonyResponse::HTTP_FORBIDDEN);
        }

        switch ($code) {
            case SymfonyResponse::HTTP_UNAUTHORIZED:
                throw new \src\PointToolsBundle\Exception\Api\UnauthorizedException($reason, $code);
            case SymfonyResponse::HTTP_FORBIDDEN:
                throw new \src\PointToolsBundle\Exception\Api\ForbiddenException($reason, $code);
            case SymfonyResponse::HTTP_NOT_FOUND:
                throw new \src\PointToolsBundle\Exception\Api\NotFoundException($reason, $code);
            case SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR:
            case SymfonyResponse::HTTP_NOT_IMPLEMENTED:
            case SymfonyResponse::HTTP_BAD_GATEWAY:
            case SymfonyResponse::HTTP_SERVICE_UNAVAILABLE:
            case SymfonyResponse::HTTP_GATEWAY_TIMEOUT:
                throw new \src\PointToolsBundle\Exception\Api\ServerProblemException($reason, $code);
        }
    }
}
