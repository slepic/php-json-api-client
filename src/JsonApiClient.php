<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

class JsonApiClient implements JsonApiClientInterface
{
    private JsonClientInterface $client;
    private string $baseUrl;

    public function __construct(JsonClientInterface $client, string $baseUrl)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    public function call(
        string $method,
        string $endpoint,
        array $query = [],
        array $headers = [],
        $body = null
    ): JsonResponseInterface {
        return $this->client->call(
            $this->baseUrl,
            $method,
            $endpoint,
            $query,
            $headers,
            $body
        );
    }
}
