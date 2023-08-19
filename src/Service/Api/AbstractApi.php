<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Exception\Api\{ApiException,
    ForbiddenException,
    NetworkException,
    NotFoundException,
    UnauthorizedException,
    ServerProblemException};
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

abstract class AbstractApi
{
    protected HttpClientInterface $client;
    // TODO: check if these are still needed
    protected string $authToken;
    protected string $csRfToken;

    public function __construct(
        HttpClientInterface $pointApiClient,
        protected readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
    ) {
        $this->client = $pointApiClient;
    }

    /** Make GET request and return DTO objects */
    public function getGetJsonData(string $path, array $parameters = [], string $type, array $groups = []): array|object|null
    {
        return $this->serializer->deserialize(
            $this->getGetResponseBody($path, $parameters),
            $type,
            'json',
            $this->createContext($groups),
        );
    }

    /** Make POST request and return DTO objects */
    public function getPostJsonData(string $path, array $parameters = [], string $type, array $groups = []): array|object|null
    {
        return $this->serializer->deserialize(
            $this->getPostResponseBody($path, $parameters),
            $type,
            'json',
            $this->createContext($groups)
        );
    }

    /** Make GET request and return response body */
    public function getGetResponseBody($path, array $parameters = []): string
    {
        return $this->sendGetRequest($path, $parameters)->getContent();
    }

    /** Make POST request and return response body */
    public function getPostResponseBody(string $path, array $parameters = []): string
    {
        return $this->sendPostRequest($path, $parameters)->getContent();
    }

    private function createContext(array $groups): array
    {
        return (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
            ->toArray();
    }
    
    private function sendGetRequest(string $path, array $parameters = []): ResponseInterface
    {
        $this->logger->debug('Sending GET request', ['path' => $path, 'parameters' => $parameters]);

        return $this->sendRequest('GET', $path, ['query' => $parameters]);
    }

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
        } catch (TransportExceptionInterface $e) {
            $this->throwIfCodeMatches($e->getCode(), $e->getPrevious());

            throw new NetworkException('Request error', $e->getCode(), $e);
        }
    }

    /** @throws ApiException */
    private function checkResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();

        // @todo remove after fix
        // Temporary fix until @arts fixes this bug
        if ('{"error": "UserNotFound"}' === $response->getContent()) {
            throw new NotFoundException('Not found', SymfonyResponse::HTTP_NOT_FOUND);
        } elseif ('{"message": "Forbidden", "code": 403, "error": "Forbidden"}' === (string) $response->getContent()) {
            throw new ForbiddenException('Forbidden', SymfonyResponse::HTTP_FORBIDDEN);
        }

        $this->throwIfCodeMatches($code);
    }

    private function throwIfCodeMatches(int $code, ?\Throwable $previous = null): void
    {
        $e = $this->matchException($code, $previous);

        if ($e) {
            throw $e;
        }
    }

    private function matchException(int $code, ?\Throwable $previous = null): ?ApiException
    {
        return match ($code) {
            SymfonyResponse::HTTP_UNAUTHORIZED => new UnauthorizedException(previous: $previous),
            SymfonyResponse::HTTP_NOT_FOUND => new NotFoundException(previous: $previous),
            SymfonyResponse::HTTP_FORBIDDEN => new ForbiddenException(previous: $previous),
            SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR,
            SymfonyResponse::HTTP_NOT_IMPLEMENTED,
            SymfonyResponse::HTTP_BAD_GATEWAY,
            SymfonyResponse::HTTP_SERVICE_UNAVAILABLE,
            SymfonyResponse::HTTP_GATEWAY_TIMEOUT => new ServerProblemException(previous: $previous),
            default => null,
        };
    }
}
