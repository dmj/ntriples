<?php

require __DIR__ . '/../vendor/autoload.php';

$graph = new HAB\NTriples\Graph();

$reader = new HAB\NTriples\Reader\BEACON();
$reader->open(__DIR__ . '/beacon.txt');

while ($triple = $reader->read()) {
    call_user_func_array(array($graph, 'add'), $triple);
}

$reader->close();

$serializer = new HAB\NTriples\Serializer\NTriples();
echo $serializer->serialize($graph);
