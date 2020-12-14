<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

class JsonResponse implements JsonResponseInterface
{
    private int $status;
    private array $headers;
    private array $body;

    /**
     * @param int $status
     * @param string[] $headers Lower case header names
     * @param array<mixed> $body
     */
    public function __construct(int $status, array $headers, array $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers[\strtolower($name)] ?? '';
    }

    public function getParsedBody(): array
    {
        return $this->body;
    }
}
