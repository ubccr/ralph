<?php
namespace CCR\Ralph;

use GuzzleHttp\Psr7\{Response, Stream};
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use CCR\Ralph\Exception\InvalidUriException;
use CCR\Ralph\Fixture\MockClient;
use function fopen, fwrite, rewind;

class ClientTest extends TestCase
{
    public function testQuery()
    {
        $handler = function (string $method, string $uri, array $options): ResponseInterface {
            $this->assertEquals(
                $options['query']['query'],
                'PREFIX fs: <http://fake/schema> SELECT * WHERE { ?p s ?o }'
            );
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, json_encode([
                'results' => [
                    'distinct' => false,
                    'ordered' => false,
                    'bindings' => [
                        'type' => 'value'
                    ]
                ]
            ]));
            rewind($stream);

            return (new Response())
                ->withBody(new Stream($stream));
        };
        $actual = (new Client(new MockClient($handler)))
            ->withEndpoint('http://localhost/sparql')
            ->withPrefix('fs', 'http://fake/schema')
            ->query('
                SELECT *
                WHERE
                {
                    ?p s ?o
                }
            ');

        $this->assertEquals(['type' => 'value'], $actual->toArray());
    } // testQuery()

    public function testInvalidEndpointUri()
    {
        $this->expectException(InvalidUriException::class);
        (new Client(new MockClient(function () {
        })))->withEndpoint('invalid');
    } // testInvalidUri()

    public function testInvalidNamespaceUri()
    {
        $this->expectException(InvalidUriException::class);
        (new Client(new MockClient(function () {
        })))->withPrefix('invalid', 'invalid');
    } // testInvalidUri()
} // class ClientTest
