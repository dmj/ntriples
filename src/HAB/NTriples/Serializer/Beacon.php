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

use InvalidArgumentException;

/**
 * Serialize graph or graphs as BEACON.
 *
 * @see       https://de.wikipedia.org/wiki/Wikipedia:BEACON/Format
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Beacon implements SerializerInterface, TripleSerializerInterface
{

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
     * Length of object prefix.
     *
     * @var integer
     */
    private $objectPrefixLength;

    /**
     * Constructor.
     *
     * @param  string $pattern
     * @return void
     */
    public function __construct ($subjectPattern, $objectPrefix, $relatingProperty)
    {
        $this->setSubjectPattern($subjectPattern);
        $this->setObjectPrefix($objectPrefix);
        $this->setRelatingProperty($relatingProperty);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize ()
    {
        $graphs = func_get_args();
        $buffer = array();
        $buffer []= '#FORMAT: BEACON';
        $buffer []= '#PREFIX: ' . $this->objectPrefix;
        $buffer []= '#TARGET: ' . $this->subjectPattern;
        foreach ($graphs as $graph) {
            foreach ($graph as $triple) {
                $triple = call_user_func_array(array($this, 'serializeTriple'), $triple);
                if ($triple) {
                    $buffer []= $triple;
                }
            }
        }
        return implode(PHP_EOL, $buffer);
    }

    /**
     * {@inheritDoc}
     */
    public function serializeTriple ($s, $p, $o)
    {
        if (!is_string($s) or !is_string($o) or ($p !== $this->relatingProperty)) {
            return;
        }
        if (strpos($o, $this->objectPrefix) !== 0) {
            return;
        }
        preg_match($this->subjectPatternRegex, $s, $match);
        if (!isset($match['ID'])) {
            return;
        }
        return $match['ID'];
    }

    /**
     * Set subject pattern.
     *
     * @throws InvalidArgumentException Invalid regular expression
     *
     * @param  string $pattern
     * @return void
     */
    private function setSubjectPattern ($subjectPattern)
    {
        $regex = '@' . preg_quote($subjectPattern, '@') . '@u';
        $regex = str_replace('\{ID\}', '(?<ID>[^/]+)', $regex);
        if (@preg_match($regex, '') === false) {
            throw new InvalidArgumentException(sprintf("Pattern '%s' is not a valid regular expression", $regex));
        }
        $this->subjectPattern = $subjectPattern;
        $this->subjectPatternRegex = $regex;
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
        $this->objectPrefixLength = strlen($objectPrefix);
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