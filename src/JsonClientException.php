<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

class JsonClientException extends \Exception implements JsonClientExceptionInterface
{
    private ?JsonResponseInterface $response;
    private string $rawBody;
    private bool $bodyParsed;

    public function __construct(
        string $message,
        ?JsonResponseInterface $response,
        string $rawBody,
        bool $bodyParsed,
        ?\Throwable $previous = null
    ) {
        $this->response = $response;
        $this->rawBody = $rawBody;
        $this->bodyParsed = $bodyParsed;
        parent::__construct($message, $response ? $response->getStatusCode() : 0, $previous);
    }

    public function getResponse(): ?JsonResponseInterface
    {
        return $this->response;
    }

    public function hasParsedBody(): bool
    {
        return $this->response && $this->bodyParsed;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }
}
