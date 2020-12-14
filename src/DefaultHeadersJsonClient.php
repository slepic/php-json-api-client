<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

/**
 * Wraps a JsonClientInterface instance along with a set of default headers to send with every request.
 */
class DefaultHeadersJsonClient implements JsonClientInterface
{
    private JsonClientInterface $client;
    private array $defaultHeaders;

    public function __construct(JsonClientInterface $client, array $defaultHeaders)
    {
        $this->client = $client;
        $this->defaultHeaders = $defaultHeaders;
    }

    public function call(
        string $baseUrl,
        string $method,
        string $endpoint,
        array $query = [],
        array $headers = [],
        $body = null
    ): JsonResponseInterface {
        $usedHeaders = [];
        $allHeaders = [];
        foreach (\array_merge($this->defaultHeaders, $headers) as $headerName => $headerValue) {
            $lowerCased = \strtolower($headerName);
            if (isset($usedHeaders[$lowerCased])) {
                unset($allHeaders[$usedHeaders[$lowerCased]]);
            }
            $usedHeaders[$lowerCased] = $headerName;
            $allHeaders[$headerName] = $headerValue;
        }

        return $this->client->call($baseUrl, $method, $endpoint, $query, $allHeaders, $body);
    }
}
