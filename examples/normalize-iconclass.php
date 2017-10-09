<?php

/**
 * Normalize whitespaces in Iconclass classification codes.
 *
 * @see http://www.iconclass.nl/contents-of-iconclass
 *
 */

require_once __DIR__ . '/../vendor/autoload.php';

function normalize_whitespace ($value) {
    $len = strlen($value);
    $keep = false;
    $normalized = '';
    for ($i = 0; $i < $len; $i++) {
        $octet = $value[$i];
        if ($octet === '(') {
            $keep = true;
        }
        if ($octet === ')') {
            $keep = false;
        }
        if ($octet !== ' ' or $keep === true) {
            $normalized .= $octet;
        }
    }
    return $normalized;
}

$reader = new HAB\NTriples\Reader\NTriples();
$serializer = new HAB\NTriples\Serializer\NTriples();

$outfile = fopen('php://stdout', 'wb');

$reader->open(__DIR__ . '/iconclass.nt');
$count = 0;
while ($triple = $reader->read()) {
    list($s, $p, $o) = $triple;
    if ($o instanceof HAB\NTriples\Literal and $o->getDatatype() === 'http://uri.hab.de/ontology/diglib-types#Iconclass') {
        $o = new HAB\NTriples\Literal(normalize_whitespace($o->getValue()), null, $o->getDatatype());
    }
    fwrite($outfile, $serializer->serializeTriple($s, $p, $o));
    fwrite($outfile, PHP_EOL);
}
$reader->close();

fclose($outfile);