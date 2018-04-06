# Ralph

**R**DF **A**bstraction **L**ayer for **PH**P: A simple SPARQL client built on top of Guzzle.

Take a look at the `list` target in the Makefile for how to use `make` to manage the project.

### An example of a query against the British Museum SPARQL endpoint:

```php
$result = (new CCR\Sparql\SparqlClient(new GuzzleHttp\Client()));
    ->withEndpoint('http://collection.britishmuseum.org/sparql')
    ->withPrefix('crm', 'http://erlangen-crm.org/current/')
    ->withPrefix('fts', 'http://www.ontotext.com/owlim/fts#');
    ->query('
        SELECT DISTINCT ?obj
        {
            ?obj crm:P102_has_title ?title .
            ?title rdfs:label ?label .
            FILTER(STR(?label) = "Hoa Hakananai\'a")
        }
    ');
print_r($result);
```
