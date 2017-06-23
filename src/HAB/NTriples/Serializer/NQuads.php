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

/**
 * Serialize graph as N-Quads.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2017 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class NQuads implements SerializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function serialize ()
    {
        $graphs = func_get_args();
        $buffer = array();
        foreach ($graphs as $graph) {
            $graphName = $graph->getName();
            foreach ($graph as $triple) {
                $triple = $this->serializeQuad($triple[0], $triple[1], $triple[2], $graphName);
                if ($triple) {
                    $buffer []= $triple;
                }
            }
        }
        return implode(PHP_EOL, $buffer);
    }

    /**
     * Serialize the Quad.
     *
     * @param  string|BNode         $s Subject
     * @param  string               $p Predicate
     * @param  string|BNode|Literal $o Object
     * @param  string|BNode         $g Graph
     * @return string
     */
    public function serializeQuad ($s, $p, $o, $g = null)
    {
        $s = $this->pattern($s);
        $p = $this->pattern($p);
        $o = $this->pattern($o);
        $g = $this->pattern($g);
        if ($s and $p and $o) {
            $line = $s . ' ' . $p . ' ' . $o . ' ';
            if ($g) {
                $line .= $g . ' ';
            }
            return $line . ' .';
        }
    }

    /**
     * Return pattern for token.
     *
     * @param  BNode|Literal|string $token
     * @return string|null
     */
    public function pattern ($token)
    {
        if ($token instanceof BNode) {
            return sprintf('_:%s', $token);
        }
        if ($token instanceof Literal) {
            $pattern = '"' . addslashes($token->getValue()) . '"';
            $language = $token->getLanguage();
            $datatype = $token->getDatatype();
            if ($language and $datatype) {
                return null;
            }
            if ($language) {
                $pattern .= '@' . $language;
            }
            if ($datatype) {
                $pattern .= '^^<' . $datatype . '>';
            }
            return $pattern;
        }
        if (is_string($token)) {
            return '<' . $token . '>';
        }
        return null;
    }
}