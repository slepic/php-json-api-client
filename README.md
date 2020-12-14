# About

A simple PSR compatible PHP JSON HTTP API client

## Installation

Install via [Composer](https://getcomposer.org/) by running `composer require slepic/json-api-client` in your project directory.

## Usage

Wrap a PSR request factory and a PSR client with a JsonClient instance.
```
$jsonClient = new JsonClient($psrRequestFactory, $psrClient);
```

And all requests are hidden in a single method `call` with several arguments:
```
try {
    $response = $jsonClient->call(
        'https://myapi.com',
        'POST',
        '/some/endpoint',
        ['queryParam' => 'value'], // query
        ['Authorization' => 'Basic u:p'], // headers
        ['bodyProperty' => 'value'] // body
    );
} catch (JsonClientExceptionInterface $e) {
    $response = $e->getResponse();
    
    // network errors?
    if (!$response) {
        throw $e;
    }

    // statuses not 2xx?
    if ($response->getStatusCode() === 404) {
        return null;
    }

    // response content type not json or json parse error?
    if (!$e->hasParsedBody()) {
        // any status code, even 2xx, can end up here if json cannot be parsed
        var_dump($e->getStatusCode());
        var_dump($e->getRawBody());
        throw $e;
    }

    throw $e;
}

if ($response->getStatusCode() !== 201) {
    // response is returned for any 2xx status code
    // so if you want to be more specific, you can do it here
    // other statuses end up in the catch block
    throw \Exception();
}
return $response->getParsedBody();
```

The JsonClient is a stateless service (as long as the underlying response factory and client are).
You can use just one instance for all your backend-backend calls.

To simplify calling a single backend API on a single domain
a reduced interface is provided via adapter:
```
$apiClient = new JsonApiClient($jsonClient, 'https://myapi.com');
```

The usage is almost the same except the base URL argument is not present.
```
$response = $apiClient->call(
    'POST',
    '/some/endpoint',
    ['queryParam' => 'value'], // query
    ['Authorization' => 'Basic u:p'], // headers
    ['bodyProperty' => 'value']
);
```
As you can see we specified base URL of the API, but we are passing some authorization headers to the call method.

For this case we provide a client decorator that adds some headers.
```
$decoratedJsonClient = new DefaultHeadersJsonClient($jsonClient, [
  'Authorization' => 'Basic u:p',
  'User-Agent' => 'mybackend/' . $backendVersion,
])
```

This one still needs the base URL, but it implements the underlying JsonClientInterface
so we can instead wrap this one in the JsonApiClient.

```
$decoratedApiClient = new JsonApiClient(
    $decoratedJsonClient,
    'https://myapi.com'
);
```

The resulting response of all the clients is the same.
It is a JsonResponseInterface instance with methods resembling those of PSR response.
But only simplified API is provided. The response object has these 4 methods:
```
public function getStatusCode(): int;
public function getHeaders(): array;
public function getHeaderLine(string $name): string;
public function getParsedBody(): array;
```
Semantics of getStatusCode() and getHeaderLine() is the same as that of PSR response methods.

Method getHeaders() acts a bit differently, it returns lowercase header names as keys and just one header line for each header.

Objects in the response json are represented as associative arrays (when returned from getParsedBody method).

## Limitations

Designed to work with pure JSON APIs, for calls involving any other content-type
in either requests or responses (or both) will have to be handled by other means.
Although the current api exceptions have a getRawBody method,
which could potentially suffice in some simple casee of non json responses.

But maybe that method will instead contain a length limited part of the raw body in future..
At current state this makes a limitation that responses that end up as exception
carries the entire payload in memory and thus may be unsuitable if the payload can be big.

