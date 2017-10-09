# N-Triples

A simple library for publishing data encoded as N-Triples.

This package is copyright (c) 2015-2017 by Herzog August Bibliothek
Wolfenbüttel and released under the terms of the GNU General Public License
v3.

## Installation

You can install this package via composer

```
composer require hab/ntriples
```

## Internal representation of graphs and triples

### Triple

a **triple** is represented as an array with subject, predicate and object as
members at the position 0, 1, and 2 respectively.

### URI

A **URI** is represented as a string.

### Literal

a **literal** is represented as an instance of the `Literal` class. A Literal
has a value and can have an optional datatype or language tag.

```
$literal = new HAB\NTriples\Literal('Hello, my darling', 'en');
echo $literal->getLanguage();

=> en
```

### Blank Node

a **blank node** is represented as an instance of the `BNode` class. If
coerced to a string it returns a unique node label.

```
$node = new HAB\NTriples\BNode();
echo (string)$node;

=> bnode_4c28e7db9528288b7519a1f5df639cf7
```

### Graph

A **graph** is represented as an instance of the `Graph` class. It has an
optional name and implements the `Traversable` interface for convenient
traversal of the graph's triples.

```
$graph = new HAB\NTriples\Graph('http://example.com/graph/1');
$graph->add('http://example.com/subject', 'http://purl.org/dc/elements/1.1/title', 'The Example');
echo count($graph);

=> 1
```

## Readers

All Readers adhere to the same interface. You first call `open()` with the URI
of the source to read form. Subsequent calls to `read()` return an array with
subject, predicate and object of the next triple or `false` if the source is
exhausted.

You can call `rewind()` to try to rewind the source. A `RuntimeException` is
thrown if the source does not support rewinding

A call to `close()` closes the reader.

### NTriples

## Serializers

### NTriples

### RDF/XML

### TriX

### NQuads
