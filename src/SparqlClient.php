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
     * Sets the endpoint for connecting to the SPARQL server.
     *
     * @param string $uri The endpoint to set. Must conform to RFC 3986.
     *
     * @return SparqlClient The clone of this client with the base URI set.
     *
     * @throws InvalidUriException If the endpoint is not a valid RFC 3986 URI.
     */

    public function withEndpoint($endpoint)
    {
        if ( filter_var($endpoint, FILTER_VALIDATE_URL) === false ) {
            throw new InvalidUriException($endpoint);
        }

        $new = clone $this;
        $new->endpoint = $endpoint;

        return $new;
    } // withEndpoint()

    /**
     * Adds a prefix that will be prepended to all queries made by this client.
     * Allows for immutability and fluent-style method calls by returning a new
     * copy of this client with the prefix added, leaving the original
     * unmodified.
     *
     * @param string $name The name of the namespace.
     * @param string $namespace The URI of the namespace.
     *
     * @return SparqlClient The clone of this client with the prefix added.
     */

    public function withPrefix($name, $namespace)
    {
        $new = clone $this;
        $new->prefixes[] = "PREFIX {$name}: <{$namespace}>";

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

    private function process($query)
    {
        $prefixes = implode(' ', $this->prefixes);
        $minified = trim(preg_replace('/\s+/', ' ', $prefixes . $query));

        return $minified;
    } // process()
} // class SparqlClient
