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
 * @copyright (c) 2017 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\NTriples\Serializer;

use HAB\NTriples\Literal;
use HAB\NTriples\BNode;
use HAB\NTriples\Graph;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the NQuads serializer class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2017 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class NQuadsTest extends TestCase
{
    public function testPatternUri ()
    {
        $serializer = new NTriples();
        $this->assertEquals('<http://example.com>', $serializer->pattern('http://example.com'));
    }

    public function testPatternBNode ()
    {
        $serializer = new NTriples();
        $this->assertRegexp('/_:[^ ]+/u', $serializer->pattern(new BNode()));
    }

    public function testPatternLiteralSimple ()
    {
        $serializer = new NTriples();
        $this->assertEquals('" EXAMPLE \\\\n "', $serializer->pattern(new Literal(' EXAMPLE \n ')));
    }

    public function testPatternLiteralDatatype ()
    {
        $serializer = new NTriples();
        $this->assertEquals('"EXAMPLE"^^<http://example.com>', $serializer->pattern(new Literal('EXAMPLE', null, 'http://example.com')));
    }

    public function testPatternLiteralLanguage ()
    {
        $serializer = new NTriples();
        $this->assertEquals('"EXAMPLE"@de', $serializer->pattern(new Literal('EXAMPLE', 'de', null)));
    }

    public function testPatternLiteralAndLanguage ()
    {
        $serializer = new NTriples();
        $this->assertNull($serializer->pattern(new Literal('EXAMPLE', 'de', 'http://example.com')));
    }

    public function testSerializeNamedGraph ()
    {
        $graph = new Graph('http://example.com/graph');
        $graph->add('http://example.com/subject', 'http://example.com/predicate', 'http://example.com/object');
        $serializer = new NQuads();
        $output = $serializer->serialize($graph);
        $this->assertNotEmpty($output);
        $output = explode(' ', $output);
        $this->assertEquals('<http://example.com/graph>', $output[3]);
    }

    public function testSerializeUnnamedGraph ()
    {
        $graph = new Graph();
        $graph->add('http://example.com/subject', 'http://example.com/predicate', 'http://example.com/object');
        $serializer = new NQuads();
        $output = $serializer->serialize($graph);
        $this->assertNotEmpty($output);
        $output = explode(' ', $output);
        $this->assertEquals('<http://example.com/object>', $output[2]);
    }
}