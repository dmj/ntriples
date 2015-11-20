<?php

/**
 * This file is part of HAB NTriples.
 *
 * HAB NTriples is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HAB NTriples is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HAB NTriples.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\NTriples\Serializer;

use HAB\NTriples\Graph;
use HAB\NTriples\Literal;

use DOMDocument;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the RDF/XML serializer class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RdfXmlTest extends TestCase
{
    public function testAbbreviate ()
    {
        $testcases = array(
            'http://www.w3.org/2004/02/skos/core#prefLabel' => array('http://www.w3.org/2004/02/skos/core#', 'prefLabel'),
            'http://purl.org/dc/elements/1.1/subject' => array('http://purl.org/dc/elements/1.1/', 'subject'),
            '#fragment' => false,
            'http://example.com/' => false
        );
        $serializer = new RdfXml();
        foreach ($testcases as $uri => $abbreviation) {
            $this->assertEquals($abbreviation, $serializer->abbreviate($uri), sprintf('Abbreviating URI %s', $uri));
        }
    }

    public function testSerialize ()
    {
        $graph = new Graph();
        $graph->add('http://example.com', 'http://purl.org/dc/elements/1.1/identifier', new Literal('EXAMPLE', null, 'http://datatype.example.com'));
        $graph->add('http://example.com', 'http://purl.org/dc/elements/1.1/identifier', 'http://id.example.com');
        $graph->add('http://example.com', 'http://purl.org/dc/elements/1.1/subject', new Literal('<'));
        $graph->add('http://example.com?arg&arg', 'http://purl.org/dc/elements/1.1/subject', new Literal(''));
        $serializer = new RdfXml();
        $document = new DOMDocument();
        $this->assertTrue($document->loadXml($serializer->serialize($graph)));
    }
}