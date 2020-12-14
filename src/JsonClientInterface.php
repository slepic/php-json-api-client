<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

/**
 * Use this client if you want to talk to multiple APIs (on different domains).
 * If you want to talk to a single API, you might want to @see JsonApiClientInterface
 */
interface JsonClientInterface
{
    /**
     * @param string $baseUrl
     * @param string $method
     * @param string $endpoint
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @param array|object|null $body
     * @return JsonResponseInterface
     * @throws JsonClientExceptionInterface
     */
    public function call(
        string $baseUrl,
        string $method,
        string $endpoint,
        array $query = [],
        array $headers = [],
        $body = null
    ): JsonResponseInterface;
}
