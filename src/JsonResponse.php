<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

class JsonResponse implements JsonResponseInterface
{
    private int $status;
    private array $headers;
    private string $rawBody;
    private array $parsedBody;

    /**
     * @param int $status
     * @param string[] $headers Lower case header names
     * @param string $rawBody
     * @param array<mixed> $parsedBody
     */
    public function __construct(int $status, array $headers, string $rawBody, array $parsedBody)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->rawBody = $rawBody;
        $this->parsedBody = $parsedBody;
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

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }
}
