<?php
namespace CCR\Ralph\Fixture;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

class MockClient implements ClientInterface
{
    private $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    } // __construct()

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return ($this->handler)($request, $options);
    } // send()

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return ($this->handler)($request, $options);
    } // sendAsync()

    public function request($method, $uri, array $options = []): ResponseInterface
    {
        return ($this->handler)($method, $uri, $options);
    } // request()

    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        return ($this->handler)($method, $uri, $options);
    } // requestAsync()

    public function getConfig($option = null)
    {
        return ($this->handler)($option);
    } // getConfig()
} // MockClient
