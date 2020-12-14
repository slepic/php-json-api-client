<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

/**
 * Use this client if you want to talk to a single API (on a single domain).
 * If you need to talk to multiple APIs, @see JsonClientInterface
 */
interface JsonApiClientInterface
{
    /**
     * @param string $method
     * @param string $endpoint
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     * @param array|object|null $body
     * @return JsonResponseInterface
     * @throws JsonClientExceptionInterface
     */
    public function call(
        string $method,
        string $endpoint,
        array $query = [],
        array $headers = [],
        $body = null
    ): JsonResponseInterface;
}
