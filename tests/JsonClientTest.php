<?php

declare(strict_types=1);

namespace Slepic\Tests\Http\JsonApiClient;

use GuzzleHttp\Psr7\Response;
use Http\Factory\Guzzle\RequestFactory;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Slepic\Http\JsonApiClient\JsonClient;
use Slepic\Http\JsonApiClient\JsonClientInterface;

class JsonClientTest extends TestCase
{
    public function testImplements(): void
    {
        $psrRequestFactory = self::createMock(RequestFactoryInterface::class);
        $psrClient = self::createMock(ClientInterface::class);
        $jsonClient = new JsonClient($psrRequestFactory, $psrClient);
        self::assertInstanceOf(JsonClientInterface::class, $jsonClient);
    }

    public function testPostSuccess(): void
    {
        $response = (new Response(203))
            ->withHeader('content-type', 'application/json')
            ->withHeader('X-Some-Header', 'my value');
        $response->getBody()->write('{"response":"value"}');

        $psrRequestFactory = new RequestFactory();
        $psrClient = self::createMock(ClientInterface::class);
        $psrClient->expects(self::once())
            ->method('sendRequest')
            ->with(new Callback(function (RequestInterface $request) {
                return (string) $request->getUri() === 'http://example.com/endpoint?q%5Bx%5D=1'
                    && $request->getMethod() === 'POST'
                    && $request->getHeaderLine('Content-Type') === 'application/json'
                    && $request->getHeaderLine('User-Agent') === 'myagent'
                    && (string) $request->getBody() === '{"json":"data"}';
            }))
            ->willReturn($response);

        $jsonClient = new JsonClient($psrRequestFactory, $psrClient);

        $output = $jsonClient->call(
            'http://example.com',
            'POST',
            '/endpoint',
            ['q' => ['x' => 1]],
            ['User-Agent' => 'myagent'],
            ['json' => 'data']
        );

        self::assertSame(203, $output->getStatusCode());
        self::assertSame('application/json', $output->getHeaderLine('Content-Type'));
        self::assertSame('my value', $output->getHeaderLine('x-some-header'));
        self::assertSame(['response' => 'value'], $output->getParsedBody());
    }
}
