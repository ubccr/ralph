# sparql-client

A simple SPARQL client built on top of Guzzle.

Look at the `list` target in the Makefile for how to use `make` to manage the project.

### Example of a query against the British Museum SPARQL endpoint:

```
$guzzle = new GuzzleHttp\Client();
$client = new CCR\Sparql\SparqlClient($guzzle);
$client = $client
    ->withEndpoint('http://collection.britishmuseum.org/sparql')
    ->withPrefix('crm', 'http://erlangen-crm.org/current/')
    ->withPrefix('fts', 'http://www.ontotext.com/owlim/fts#');
$result = $client->query('
    SELECT DISTINCT ?obj
    {
        ?obj crm:P102_has_title ?title .
        ?title rdfs:label ?label .
        FILTER(STR(?label) = "Hoa Hakananai\'a")
    }
');
print_r($result);
```
