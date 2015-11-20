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

namespace HAB\NTriples;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the Graph class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class GraphTest extends TestCase
{
    public function testCount ()
    {
        $graph = new Graph();
        $this->assertCount(0, $graph);
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');
        $this->assertCount(1, $graph);
        $graph->add('http://example.com/subject2', 'http://example.com/predicate', 'http://example.com/object');
        $this->assertCount(2, $graph);
    }

    public function testSelect ()
    {
        $graph = new Graph();
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');
        $graph->add('http://example.com/subject2', 'http://example.com/predicate', 'http://example.com/object');

        $selector = function ($triple) {
            return ($triple[0] === 'http://example.com/subject2');
        };
        $graph = $graph->select($selector);
        $this->assertCount(1, $graph);
    }

    public function testSubjects ()
    {
        $graph = new Graph();
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');
        $graph->add('http://example.com/subject2', 'http://example.com/predicate', 'http://example.com/object');
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');
        $this->assertCount(2, $graph->subjects());
    }

    public function testProperties ()
    {
        $graph = new Graph();
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');
        $graph->add('http://example.com/subject2', 'http://example.com/predicate', 'http://example.com/object');
        $graph->add('http://example.com/subject1', 'http://example.com/predicate', 'http://example.com/object');

        $props = $graph->properties('http://example.com/subject1');
        $this->assertCount(1, $props);
        $this->assertCount(2, $props['http://example.com/predicate']);
    }
}