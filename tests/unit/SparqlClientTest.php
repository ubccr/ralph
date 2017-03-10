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
                $this->assertEquals($method, 'POST');
                $this->assertEquals($uri, 'http://localhost:3030/fake/query');
                $this->assertEquals($options, [
                    'form_params' => [
                        'query' => 'PREFIX fake: <fake> SELECT ?fake WHERE { }',
                        'output' => 'json'
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
            ->withBaseUri('http://localhost:3030/fake/')
            ->withPrefix('fake', 'fake');
        $result = $client->query('
            SELECT ?fake
            WHERE
            {
            }
        ');

        $this->assertEquals($result, ['name' => 'fake']);
    } // testQuery()

    public function testInvalidUri()
    {
        $this->expectException(InvalidUriException::class);
        $this->client->withBaseUri('invalid');
    } // testInvalidUri()
} // class SparqlClientTest
