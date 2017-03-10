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
     * @var string $baseUri The base URI for the SPARQL server.
     */

    private $baseUri = '';

    /**
     * @var array $prefixes The prefixes to prepend to each request.
     */

    private $prefixes = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    } // __construct()

    /**
     * Sets the base URI for connecting to the SPARQL server.
     *
     * @param string $uri The base URI to set. Must conform to RFC 3986.
     *
     * @return SparqlClient The clone of this client with the base URI set.
     *
     * @throws DomainException If the URI is not a valid RFC 3986 URI.
     */

    public function withBaseUri($uri)
    {
        if ( filter_var($uri, FILTER_VALIDATE_URL) === false ) {
            throw new InvalidUriException($uri);
        }

        $new = clone $this;
        $new->baseUri = $uri;

        return $new;
    } // withBaseUri

    /**
     * Adds a prefix that will be prepended to all queries made by this client.
     * Allows for immutability and fluent-style method calls by returning a new
     * copy of this client with the prefix added, leaving the original
     * unmodified.
     *
     * @param string $name The name of the schema.
     * @param string $schema The URI of the schema.
     *
     * @return SparqlClient The clone of this client with the prefix added.
     */

    public function withPrefix($name, $schema)
    {
        $new = clone $this;
        $new->prefixes[] = 'PREFIX ' . $name . ': <' . $schema . '>';

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
        $prefixes = implode(' ', $this->prefixes);
        $minified = trim(preg_replace('/\s+/', ' ', $prefixes . $query));
        $response = $this->client->request('POST', $this->baseUri . 'query', [
            'form_params' => [
                'query' => $minified,
                'output' => 'json'
            ]
        ]);

        return json_decode($response->getBody(), true);
    } // query()

    /**
     * Updates the SPARQL endpoint with a INSERT, DELETE, LOAD or CLEAR update.
     *
     * @param string $update The update to execute.
     */

    public function update($update)
    {
        $prefixes = implode(' ', $this->prefixes);
        $minified = trim(preg_replace('/\s+/', ' ', $prefixes . $update));
        $this->client->request('POST', $this->baseUri . 'update', [
            'form_params' => [
                'update' => $minified,
                'output' => 'json'
            ]
        ]);
    } // update()
} // class SparqlClient
