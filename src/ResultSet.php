<?php
namespace CCR\Ralph;

use InvalidArgumentException, IteratorAggregate, JsonSerializable;
use function json_decode;

/**
 * A result set for working with results returned from a SPARQL endpoint.
 */

class ResultSet implements IteratorAggregate, JsonSerializable
{
    /** @var array Results returned from a query. */
    private $results;

    public function __construct(string $results)
    {
        if ( ($this->results = json_decode($results, true)) === null ) {
            throw new InvalidArgumentException('Failed to unserialize JSON result set string.');
        }
    } // __construct()

    /**
     * Get the variables included in the results.
     *
     * @return array The variables as an array.
     */

    public function getVariables(): ?array
    {
        return $this->results['head']['vars'] ?? null;
    } // getVariables()

    /**
     * Check whether the results are distinct.
     *
     * @return bool Whether the results are distinct or not.
     */

    public function isDistinct(): ?bool
    {
        return $this->results['results']['distinct'] ?? null;
    } // isDistinct()

    /**
     * Check whether the results are ordered.
     *
     * @return bool Whether the results are ordered or not.
     */

    public function isOrdered(): ?bool
    {
        return $this->results['results']['ordered'] ?? null;
    } // isOrdered()

    /**
     * Get the full result set as an associative array.
     *
     * @return array The full reset set.
     */

    public function toArray(): ?array
    {
        return $this->results['results']['bindings'] ?? null;
    } // toArray()

    public function getIterator(): array
    {
        return $this->toArray();
    } // getIterator()

    public function jsonSerialize(): array
    {
        return $this->toArray();
    } // jsonSerialize()
} // class ResultSet
