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
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace HAB\NTriples\Reader;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the BEACON reader class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class BeaconTest extends TestCase
{
    public function testReadTriples ()
    {
        $graph = array();
        $reader = new Beacon();
        $reader->open(__DIR__ . '/testdata.beacon');
        while ($triple = $reader->read()) {
            list($s, $p, $o) = $triple;
            $graph[$s] = $o;
        }
        $this->assertEquals('http://example.com/prefix/just-an-id', $graph['http://example.com/target/just-an-id.html']);
        $this->assertEquals('http://example.com/prefix/id-and-hit', $graph['http://example.com/target/id-and-hit.html']);
        $this->assertEquals('http://example.com', $graph['http://example.com/target/id-and-hit-and-target.html']);
        $this->assertEquals('http://example.com', $graph['http://example.com/target/id-and-target.html']);
    }
}