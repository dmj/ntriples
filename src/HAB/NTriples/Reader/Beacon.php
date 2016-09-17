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

/**
 * Read triples for a BEACON file.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Beacon
{
    /**
     * Regular expression matching BEACON data.
     *
     * @var string
     */
    private static $beaconDataRe = '@^(?<id>[^#][^|]*)(\|(?<hits>[0-9]*)(\|(?<target>.*))?)?@u';

    /**
     * Relating property.
     *
     * @var string
     */
    private $relatingProperty = 'http://purl.org/dc/terms/references';

        /**
     * Subject pattern as URL template.
     *
     * @var string
     */
    private $subjectPattern;

    /**
     * Subject pattern expression.
     *
     * @var string
     */
    private $subjectPatternRegex;

    /**
     * Object prefix.
     *
     * @var string
     */
    private $objectPrefix;

    /**
     * File handle for read operation.
     *
     * @var resource
     */
    private $handle;
    /**
     * Constructor.
     *
     * @param  string $relatingProperty
     * @return void
     */
    public function __construct ($relatingProperty = null)
    {
        if ($relatingProperty) {
            $this->setRelatingProperty($relatingProperty);
        }
    }

    /**
     * Open reader to read from given URI.
     *
     * @param  string $uri
     * @return void
     */
    public function open ($uri)
    {
        $this->handle = fopen($uri, 'r');
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
            if ($line and $line[0] === '#') {
                $this->handleHeaderLine($line);
            } else if (preg_match(self::$beaconDataRe, $line, $match)) {
                $id = $match['id'];
                $hits = isset($match['hits']) ? $match['hits'] : null;
                $target = isset($match['target']) ? $match['target'] : null;
                $triple = $this->createTriple($id, $hits, $target);
            }
        }
        return $triple;
    }

    /**
     * Create triple from BEACON data.
     *
     * @param  string  $id
     * @param  integer $hits
     * @param  string  $target
     * @return array()
     */
    private function createTriple ($id, $hits = null, $target = null)
    {
        $s = str_replace('{ID}', $id, $this->subjectPattern);
        $p = $this->relatingProperty;
        $o = $target ?: $this->objectPrefix . $id;
        return array($s, $p, $o);
    }

    /**
     * Handle a BEACON header line.
     *
     * @param  string $line
     * @return void
     */
    private function handleHeaderLine ($line)
    {
        $line = explode(':', substr($line, 1), 2);
        if (count($line) === 2) {
            $directive = trim($line[0]);
            $value = trim($line[1]);
            if ($directive === 'PREFIX') {
                $this->objectPrefix = $value;
            }
            if ($directive === 'TARGET') {
                $this->subjectPattern = $value;
            }
        }
    }

    /**
     * Set subject pattern.
     *
     * @param  string $pattern
     * @return void
     */
    private function setSubjectPattern ($subjectPattern)
    {
        $this->subjectPattern = $subjectPattern;
    }

    /**
     * Set object prefix.
     *
     * @param  string $objectPrefix
     * @return void
     */
    private function setObjectPrefix ($objectPrefix)
    {
        $this->objectPrefix = $objectPrefix;
    }

    /**
     * Set relatingProperty.
     *
     * @param  string $relatingProperty
     * @return void
     */
    private function setRelatingProperty ($relatingProperty)
    {
        $this->relatingProperty = $relatingProperty;
    }

}