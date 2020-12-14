<?php

declare(strict_types=1);

namespace Slepic\Http\JsonApiClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Slexphp\Serialization\Contracts\Decoder\DecodeExceptionInterface;
use Slexphp\Serialization\Contracts\Decoder\DecoderInterface;
use Slexphp\Serialization\Contracts\Encoder\EncodeExceptionInterface;
use Slexphp\Serialization\Contracts\Encoder\EncoderInterface;

class JsonClient implements JsonClientInterface
{
    private RequestFactoryInterface $requestFactory;
    private ClientInterface $client;
    private EncoderInterface $encoder;
    private DecoderInterface $decoder;

    /**
     * @param RequestFactoryInterface $requestFactory
     * @param ClientInterface $client
     * @param EncoderInterface<array|object> $encoder
     * @param DecoderInterface<array> $decoder
     */
    public function __construct(
        RequestFactoryInterface $requestFactory,
        ClientInterface $client,
        EncoderInterface $encoder,
        DecoderInterface $decoder
    ) {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function call(
        string $baseUrl,
        string $method,
        string $endpoint,
        array $query = [],
        array $headers = [],
        $body = null
    ): JsonResponseInterface {
        $uri = $baseUrl
            . ($endpoint !== '' && $endpoint[0] !== '/' ? '/' : '')
            . $endpoint
            . ($query ? '?' . \http_build_query($query) : '');
        $request = $this->requestFactory->createRequest($method, $uri);

        foreach ($headers as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, $headerValue);
        }

        if ($body !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            try {
                $requestJson = $this->encoder->encode($body);
            } catch (EncodeExceptionInterface $e) {
                throw new JsonClientException(
                    'Cannot encode input json: ' . $e->getMessage(),
                    null,
                    '',
                    false,
                    $e
                );
            }
            $request->getBody()->write($requestJson);
        } else {
            $request = $request->withoutHeader('Content-Type');
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new JsonClientException(
                "Failed request $method $uri: " . $e->getMessage(),
                null,
                '',
                false,
                $e
            );
        }

        $responseHeaders = [];
        foreach (\array_keys($response->getHeaders()) as $headerName) {
            $responseHeaders[\strtolower($headerName)] = $response->getHeaderLine($headerName);
        }

        $status = $response->getStatusCode();
        $responseJson = [];
        $responseBody = (string) $response->getBody();
        if ($responseBody !== '') {
            $contentType = $response->getHeaderLine('Content-Type');
            if ($contentType && \strpos($contentType, 'application/json') === false) {
                throw new JsonClientException(
                    "Cannot decode response json of $method $uri, because content-type is $contentType",
                    new JsonResponse($status, $responseHeaders, []),
                    $responseBody,
                    false
                );
            }

            try {
                $responseJson = $this->decoder->decode($responseBody);
            } catch (DecodeExceptionInterface $e) {
                throw new JsonClientException(
                    "Could not decode json response of $method $uri ($status): " . $e->getMessage(),
                    new JsonResponse($status, $responseHeaders, []),
                    $responseBody,
                    false,
                    $e
                );
            }

            if (!\is_array($responseJson)) {
                throw new JsonClientException(
                    "JSON response does not contain array or object for $method $uri ($status)",
                    new JsonResponse($status, $responseHeaders, []),
                    $responseBody,
                    false
                );
            }
        }

        if ($status < 200 || $status >= 300) {
            throw new JsonClientException(
                "Received error status code $status for $method $uri",
                new JsonResponse($status, $responseHeaders, $responseJson),
                $responseBody,
                true
            );
        }

        return new JsonResponse($status, $responseHeaders, $responseJson);
    }
}
