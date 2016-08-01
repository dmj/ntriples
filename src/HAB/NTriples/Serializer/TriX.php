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

/**
 * Serialize graph as TriX.
 *
 * @see       http://www.hpl.hp.com/techreports/2004/HPL-2004-56.html
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class TriX implements SerializerInterface, TripleSerializerInterface
{
    /**
     * {@inheritDoc}
     */
    public function serialize ()
    {
        $graphs = func_get_args();
        $buffer = array();
        $buffer []= '<TriX xmlns="http://www.w3.org/2004/03/trix/trix-1/">';
        foreach ($graphs as $graph) {
            $buffer []= '<graph>';
            if ($name = $graph->getName()) {
                $buffer []= $this->pattern($name);
            }
            foreach ($graph as $triple) {
                $triple = call_user_func_array(array($this, 'serializeTriple'), $triple);
                if ($triple) {
                    $buffer []= $triple;
                }
            }
            $buffer []= '</graph>';
        }
        $buffer []= '</TriX>';
        return implode(PHP_EOL, $buffer);
    }

    /**
     * {@inheritDoc}
     */
    public function serializeTriple ($s, $p, $o)
    {
        $s = $this->pattern($s);
        $p = $this->pattern($s);
        $o = $this->pattern($s);
        if ($s and $p and $o) {
            return '<triple>' . $s . $p . $o . '</triple>';
        }
    }

    /**
     * Return pattern for token.
     *
     * @param  BNode|Literal|string $token
     * @return string
     */
    public function pattern ($token)
    {
        if (is_string($token)) {
            return sprintf('<uri>%s</uri>', htmlspecialchars(trim($token), ENT_QUOTES|ENT_XML1));
        }
        if ($token instanceof BNode) {
            return sprintf('<id>%s</id>', $token);
        }
        if ($token instanceof Literal) {
            $value = htmlspecialchars($token->getValue(), ENT_QUOTES|ENT_XML1);
            $language = htmlspecialchars($token->getLanguage(), ENT_QUOTES|ENT_XML1);
            $datatype = htmlspecialchars($token->getDatatype(), ENT_QUOTES|ENT_XML1);
            if ($datatype) {
                return sprintf('<typedLiteral datatype="%s">%s</typedLiteral>', $datatype, $value);
            }
            return sprintf('<plainLiteral %s>%s</plainLiteral>', $language ? sprintf('xml:lang="%s"', $language) : '', $value);
        }
        return null;
    }
}