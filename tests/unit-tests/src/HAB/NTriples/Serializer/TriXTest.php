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

namespace HAB\NTriples\Serializer;

use HAB\NTriples\Literal;
use HAB\NTriples\BNode;
use HAB\NTriples\Graph;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the TriX serializer class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class TriXTest extends TestCase
{
    public function testPatternUri ()
    {
        $serializer = new TriX();
        $this->assertEquals('<uri>http://example.com</uri>', $serializer->pattern('http://example.com'));
    }

    public function testPatternBNode ()
    {
        $serializer = new TriX();
        $this->assertRegexp('@<id>[^ ]+</id>@u', $serializer->pattern(new BNode()));
    }

    public function testPatternLiteralSimple ()
    {
        $serializer = new TriX();
        $this->assertEquals('<plainLiteral > EXAMPLE \\n </plainLiteral>', $serializer->pattern(new Literal(' EXAMPLE \n ')));
    }

    public function testPatternLiteralDatatype ()
    {
        $serializer = new TriX();
        $this->assertEquals('<typedLiteral datatype="http://example.com">EXAMPLE</typedLiteral>', $serializer->pattern(new Literal('EXAMPLE', null, 'http://example.com')));
    }
}