<?php
namespace CCR\Sparql;

use CCR\Sparql\Exception\InvalidUriException;
use GuzzleHttp\ClientInterface;

/**
 * A client for connecting to and querying a SPARQL endpoint.
 */

class SparqlClient
{
    /**
     * @var Client $client The Guzzle client for communicating with the SPARQL
     *     endpoint.
     */

    private $client;

    /**
     * @var string $endpoint The endpoint for the SPARQL server.
     */

    private $endpoint = '';

    /**
     * @var array $prefixes The prefixes to prepend to each request.
     */

    private $prefixes = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    } // __construct()

    /**
     * Sets the endpoint for connecting to the SPARQL server. A client can only
     * connect to one endpoint at a time; any subsequent calls to this method
     * will replace the existing endpoint.
     *
     * @param string $endpoint The endpoint to set. Must conform to RFC 3986.
     *
     * @return SparqlClient The clone of this client with the endpoint set.
     *
     * @throws InvalidUriException If the endpoint is not a valid RFC 3986 URI.
     */

    public function withEndpoint($endpoint)
    {
        $this->validate($endpoint);
        $new = clone $this;
        $new->endpoint = $endpoint;

        return $new;
    } // withEndpoint()

    /**
     * Sets a prefix that will be prepended to all queries made by this client.
     * More than one prefix can be set; make subsequent calls to this method as
     * needed to add more prefixes.
     *
     * @param string $name The name of the namespace.
     * @param string $namespace The URI of the namespace.
     *
     * @return SparqlClient The clone of this client with the prefix added.
     *
     * @throws InvalidUriException If the namespace is not a valid RFC 3986 URI.
     */

    public function withPrefix($name, $namespace)
    {
        $this->validate($namespace);
        $new = clone $this;
        $new->prefixes[] = [
            'name' => $name,
            'namespace' => $namespace
        ];

        return $new;
    } // withPrefix()

    /**
     * Queries the SPARQL endpoint with a SELECT, ASK or DESCRIBE query and
     * returns the result set as an associative array.
     *
     * @param string $query The query to execute.
     *
     * @return array The result set.
     */

    public function query($query)
    {
        $processed = $this->process($query);
        $response = $this->client->request('GET', $this->endpoint, [
            'query' => [
                'query' => $processed,
                'format' => 'json'
            ]
        ]);

        return json_decode($response->getBody(), true);
    } // query()

    private function validate($uri)
    {
        if ( filter_var($uri, FILTER_VALIDATE_URL) === false ) {
            throw new InvalidUriException($uri);
        }
    }

    private function process($query)
    {
        $result = '';

        foreach ( $this->prefixes as $prefix ) {
            $result .= "PREFIX {$prefix['name']}: <{$prefix['namespace']}> ";
        }

        $result .= trim(preg_replace('/\s+/', ' ', $query));

        return $result;
    } // process()
} // class SparqlClient
