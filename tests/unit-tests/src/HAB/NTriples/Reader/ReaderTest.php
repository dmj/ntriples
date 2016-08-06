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

namespace HAB\NTriples\Reader;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the Reader class.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class ReaderTest extends TestCase
{
    public function testTokenUri ()
    {
        $reader = new Reader();
        $this->assertNull($reader->token('<'));
        $this->assertEquals('http://example.com', $reader->token('<http://example.com>'));
    }

    public function testTokenBNode ()
    {
        $reader = new Reader();
        $token = $reader->token('_:bnode_12345');
        $this->assertInstanceOf('HAB\NTriples\BNode', $token);
        $this->assertSame($token, $reader->token('_:bnode_12345'));
        $this->assertNotSame($token, $reader->token('_:bnode_67890'));
    }

    public function testTokenLiteralLanguage ()
    {
        $reader = new Reader();
        $token = $reader->token('" EXAMPLE \" EXAMPLE"@de');
        $this->assertInstanceOf('HAB\NTriples\Literal', $token);
        $this->assertEquals(' EXAMPLE " EXAMPLE', $token->getValue());
        $this->assertEquals('de', $token->getLanguage());
        $this->assertEquals('', $token->getDatatype());
    }

    public function testTokenLiteralDatatype ()
    {
        $reader = new Reader();
        $token = $reader->token('" EXAMPLE \" EXAMPLE"^^<http://example.com>');
        $this->assertInstanceOf('HAB\NTriples\Literal', $token);
        $this->assertEquals(' EXAMPLE " EXAMPLE', $token->getValue());
        $this->assertEquals('', $token->getLanguage());
        $this->assertEquals('http://example.com', $token->getDatatype());
    }

    public function testTokenLiteralSimple ()
    {
        $reader = new Reader();
        $token = $reader->token('" EXAMPLE \" EXAMPLE"');
        $this->assertInstanceOf('HAB\NTriples\Literal', $token);
        $this->assertEquals(' EXAMPLE " EXAMPLE', $token->getValue());
        $this->assertEquals('', $token->getLanguage());
        $this->assertEquals('', $token->getDatatype());
    }

    public function testReader ()
    {
        $reader = new Reader();
        $reader->open(__DIR__ . '/testdata.nt');
        $count = 0;
        while ($triple = $reader->read()) {
            $count++;
        }
        $reader->close();
        $this->assertEquals(7, $count);
    }

    public function testRewind ()
    {
        $reader = new Reader();
        $reader->open(__DIR__ . '/testdata.nt');
        $count = 0;
        while ($triple = $reader->read()) {
            $count++;
        };
        $this->assertEquals(7, $count);
        $reader->rewind();
        while ($triple = $reader->read()) {
            $count++;
        };
        $this->assertEquals(14, $count);
    }
}