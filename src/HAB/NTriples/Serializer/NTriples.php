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

use HAB\NTriples\Literal;
use HAB\NTriples\BNode;
use HAB\NTriples\Graph;

/**
 * Serialize graph as N-Triples.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class NTriples implements SerializerInterface, TripleSerializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function serialize ()
    {
        $graphs = func_get_args();
        $buffer = array();
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
        $s = $this->pattern($s);
        $p = $this->pattern($p);
        $o = $this->pattern($o);
        if ($s and $p and $o) {
            return $s . ' ' . $p . ' ' . $o . ' .';
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