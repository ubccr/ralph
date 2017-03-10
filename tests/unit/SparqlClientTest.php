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
                $this->assertEquals(
                    $options['query']['query'],
                    'PREFIX fs: <http://fake/schema> SELECT * WHERE { ?p a ?o }'
                );

                return Stub::makeEmpty(ResponseInterface::class, [
                    'getBody' => function () {
                        return '{"type":"value"}';
                    }
                ]);
            })
        ]);
        $client = $this->client
            ->withEndpoint('http://localhost/sparql')
            ->withPrefix('fs', 'http://fake/schema');
        $query = "
            SELECT *
            WHERE
            {
                ?p a ?o
            }
        ";
        $result = $client->query($query);

        $this->assertEquals($result, ['type' => 'value']);
    } // testQuery()

    public function testInvalidEndpointUri()
    {
        $this->expectException(InvalidUriException::class);
        $this->client->withEndpoint('invalid');
    } // testInvalidUri()

    public function testInvalidNamespaceUri()
    {
        $this->expectException(InvalidUriException::class);
        $this->client->withPrefix('i', 'invalid');
    } // testInvalidUri()
} // class SparqlClientTest
