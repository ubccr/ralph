<?php
namespace CCR\Ralph;

use GuzzleHttp\ClientInterface;
use CCR\Ralph\Exception\InvalidUriException;
use function array_reduce, filter_var, preg_replace, sprintf, trim;

/**
 * A client for connecting to and querying a SPARQL endpoint.
 */

class Client
{
    /** @var ClientInterface HTTP client. */
    private $client;

    /** @var string SPARQL endpoint. */
    private $endpoint;

    /** @var array Prefixes to prepend to each request. */
    private $prefixes = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    } // __construct()

    /**
     * Set the endpoint for connecting to the SPARQL server. A client can only
     * connect to one endpoint at a time; any subsequent calls to this method
     * will replace the existing endpoint.
     *
     * @param string $uri Endpoint URI. Must conform to RFC 3986.
     *
     * @return self This instance to allow for method chaining.
     *
     * @throws InvalidUriException If the endpoint is not a valid RFC 3986 URI.
     */

    public function withEndpoint(string $uri): self
    {
        $this->validate($uri);
        $this->endpoint = $uri;

        return $this;
    } // withEndpoint()

    /**
     * Sets a prefix that will be prepended to all queries made by this client.
     * More than one prefix can be set; make subsequent calls to this method as
     * needed to add more prefixes.
     *
     * @param string $namespace Namespace name.
     * @param string $uri Namespace URI.
     *
     * @return self This instance to allow for method chaining.
     *
     * @throws InvalidUriException If the namespace is not a valid RFC 3986 URI.
     */

    public function withPrefix(string $namespace, string $uri): self
    {
        $this->validate($uri);
        $this->prefixes[] = [$namespace, $uri];

        return $this;
    } // withPrefix()

    /**
     * Queries the SPARQL endpoint with a SELECT, ASK or DESCRIBE query and
     * returns the result set.
     *
     * @param string $query Query to execute.
     *
     * @return ResultSet The returned result set.
     */

    public function query(string $query): ResultSet
    {
        $processed = $this->process($query);
        $response = $this->client->request('GET', $this->endpoint, [
            'query' => [
                'query' => $processed,
                'format' => 'json'
            ]
        ]);

        return new ResultSet($response->getBody());
    } // query()

    private function validate(string $uri): void
    {
        if ( filter_var($uri, FILTER_VALIDATE_URL) === false ) {
            throw new InvalidUriException($uri);
        }
    }

    private function process(string $query): string
    {
        return array_reduce($this->prefixes, function (string $carry, array $item): string {
            [$namespace, $uri] = $item;

            return $carry . sprintf('PREFIX %s: <%s> ', $namespace, $uri);
        }, '') . trim(preg_replace('/\s+/', ' ', $query));
    } // process()
} // class Client
