<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

class JsonClientException extends \Exception implements JsonClientExceptionInterface
{
    private ?JsonResponseInterface $response;
    private bool $bodyParsed;

    public function __construct(
        string $message,
        ?JsonResponseInterface $response,
        bool $bodyParsed,
        ?\Throwable $previous = null
    ) {
        $this->response = $response;
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
}
