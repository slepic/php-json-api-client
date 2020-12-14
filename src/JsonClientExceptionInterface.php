<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

/**
 * Represents json client error.
 *
 * For network errors, there is no response object.
 *
 * For non 2xx status codes, there is a response object and it may or may not have a parsed body.
 *
 * For 2xx status codes there is a response object but it doesn't have a parsed body.
 */
interface JsonClientExceptionInterface extends \Throwable
{
    /**
     * @return JsonResponseInterface|null
     */
    public function getResponse(): ?JsonResponseInterface;

    /**
     * @return bool True if response body json was successfully parsed or it was empty.
     *      If the body was empty or unable to be parsed, getParsedBody of the response object must return empty array.
     */
    public function hasParsedBody(): bool;

    /**
     * @return string Raw body contents string
     */
    public function getRawBody(): string;
}
