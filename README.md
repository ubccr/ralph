# sparql-client

A simple SPARQL client built on top of Guzzle.

Look at the `list` target in the Makefile for how to use `make` to manage the project.

### Example of a query against the British Museum SPARQL endpoint:

```
$guzzle = GuzzleHttp\Client();
$client = new CCR\Sparql\SparqlClient($guzzle);
$client = $client
    ->withBaseUri('http://collection.britishmuseum.org/')
    ->withPrefix('bmo', 'http://collection.britishmuseum.org/id/object/');
$result = $client->query('
    SELECT ?p ?o
    WHERE
    {
        bmo:PPA82633 ?p ?o .
    }
');
echo $result;
```
