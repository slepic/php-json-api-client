<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

interface JsonResponseInterface
{
    /**
     * @return int Can be any HTTP status code or zero.
     */
    public function getStatusCode(): int;

    /**
     * @return array<string, string> Always lowercase header keys and only one header line each
     */
    public function getHeaders(): array;

    /**
     * @param string $name Case insensitive header name
     * @return string
     */
    public function getHeaderLine(string $name): string;

    /**
     * @return array<mixed> Associative array representation of the response json body.
     */
    public function getParsedBody(): array;
}
