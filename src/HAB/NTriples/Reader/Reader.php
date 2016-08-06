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

use HAB\NTriples\Literal;
use HAB\NTriples\BNode;

use RuntimeException;

/**
 * NTriples reader.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Reader
{
    /**
     * File handle for read operation.
     *
     * @var resource
     */
    private $handle;

    /**
     * Allocated blank nodes, indexed by label.
     *
     * @var array
     */
    private $bnodes = array();


    /**
     * Open reader to read from given URI.
     *
     * @param  string $uri
     * @return void
     */
    public function open ($uri)
    {
        $this->handle = fopen($uri, 'r');
        $this->bnodes = array();
    }

    /**
     * Read from handle and return triple.
     *
     * Returns false if the input stream is exhausted.
     *
     * @return array|false
     */
    public function read ()
    {
        $triple = false;
        while (empty($triple) and !feof($this->handle)) {
            $line = trim(fgets($this->handle));
            if (preg_match('/^(?<s>[^ ]+) (?<p>[^ ]+) (?<o>(".*"[^ ]*|[^ ]+)) .$/u', $line, $match)) {
                $s = $this->token($match['s']);
                $p = $this->token($match['p']);
                $o = $this->token($match['o']);
                if ($s and $p and $o) {
                    $triple = array($s, $p, $o);
                }
            }
        }
        return $triple;
    }

    /**
     * Return token of pattern.
     *
     * @param  string $pattern
     * @return string|BNode|Literal|null
     */
    public function token ($pattern)
    {
        switch ($pattern[0]) {
            case '<':
                if (preg_match('@^<(?<uri>[^>]+)>$@u', $pattern, $match)) {
                    return $match['uri'];
                };
                break;
            case '_':
                if (preg_match('@^_:(?<label>[^ ]+)$@u', $pattern, $match)) {
                    return $this->bnode($match['label']);
                }
                break;
            case '"':
                if (preg_match('/^"(?<value>.*)"(@(?<language>[^ ]+)|\^\^<(?<datatype>[^>]+)>)?$/u', $pattern, $match)) {
                    $language = array_key_exists('language', $match) ? $match['language'] : null;
                    $datatype = array_key_exists('datatype', $match) ? $match['datatype'] : null;
                    $value = stripslashes($match['value']);
                    return new Literal($value, $language, $datatype);
                }
        }
        return null;
    }

    /**
     * Close reader.
     *
     * @return void
     */
    public function close ()
    {
        fclose($this->handle);
    }

    /**
     * Rewind input stream.
     *
     * @throws RuntimeException Cannot rewind input stream
     *
     * @return void
     */
    public function rewind ()
    {
        if (rewind($this->handle) !== true) {
            throw new RuntimeException('Unable to rewind input stream');
        }
    }

    /**
     * Return blank node.
     *
     * @param  string $label
     * @return BNode
     */
    private function bnode ($label)
    {
        if (!array_key_exists($label, $this->bnodes)) {
            $this->bnodes[$label] = new BNode();
        }
        return $this->bnodes[$label];
    }
}