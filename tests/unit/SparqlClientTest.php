<?php
namespace CCR\Sparql;

use CCR\Sparql\Exception\InvalidUriException;
use Codeception\Util\Stub;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class SparqlClientTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester $tester
     */

    protected $tester;

    /**
     * @var ClientInterface $guzzle
     */
    private $guzzle;

    /**
     * @var SparqlClient $client
     */

    private $client;

    protected function _before()
    {
        $this->guzzle = Stub::makeEmpty(ClientInterface::class);
        $this->client = new SparqlClient($this->guzzle);
    } // _before()

    // tests
    public function testQuery()
    {
        Stub::update($this->guzzle, [
            'request' => Stub::once(function ($method, $uri, array $options) {
                $this->assertEquals($method, 'GET');
                $this->assertEquals($uri, 'http://localhost/sparql');
                $this->assertEquals($options, [
                    'query' => [
                        'query' => 'PREFIX f: <fake> SELECT ?fake WHERE { }',
                        'format' => 'json'
                    ]
                ]);

                return Stub::makeEmpty(ResponseInterface::class, [
                    'getBody' => function () {
                        return '{"name":"fake"}';
                    }
                ]);
            })
        ]);
        $client = $this->client
            ->withEndpoint('http://localhost/sparql')
            ->withPrefix('f', 'fake');
        $query = "
            SELECT ?fake
            WHERE
            {
            }
        ";
        $result = $client->query($query);

        $this->assertEquals($result, ['name' => 'fake']);
    } // testQuery()

    public function testInvalidUri()
    {
        $this->expectException(InvalidUriException::class);
        $this->client->withEndpoint('invalid');
    } // testInvalidUri()
} // class SparqlClientTest
