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

use HAB\NTriples\Graph;
use HAB\NTriples\BNode;
use HAB\NTriples\Literal;

/**
 * Serialize graph as RDF/XML document.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class RdfXml
{
    /**
     * Map URIs to abbreviations.
     *
     * @var array
     */
    private $uriAbbreviationMap = array();

    /**
     * Namespace declarations.
     *
     * @var array
     */
    private $namespaceDeclMap = array();

    /**
     * Default namespace declarations.
     *
     * @var array
     */
    private $namespaceDeclMapDefault = array('http://www.w3.org/1999/02/22-rdf-syntax-ns#' => 'rdf');

    /**
     * Namespace declarion counter.
     *
     * @var integer
     */
    private $namespaceDeclCount = 0;

    /**
     * {@inheritDoc}
     */
    public function serialize (Graph $graph)
    {
        $this->createPredicateAbbreviationMaps($graph);
        
        $nsDecls = array();
        foreach ($this->namespaceDeclMap as $namespace => $prefix) {
            $nsDecls []= sprintf('xmlns:%s="%s"', $prefix, $namespace);
        }
        
        $buffer   = array(sprintf('<rdf:RDF %s>', implode(' ', $nsDecls)));
        foreach ($graph->subjects() as $subject) {
            $buffer []= sprintf('<rdf:Description rdf:%s="%s">', ($subject instanceof BNode) ? 'nodeID' : 'about', htmlspecialchars($subject, ENT_QUOTES|ENT_XML1));
            foreach ($graph->properties($subject) as $predicate => $values) {
                if (array_key_exists($predicate, $this->uriAbbreviationMap)) {
                    list($namespace, $localname) = $this->uriAbbreviationMap[$predicate];
                    $qname = $this->namespaceDeclMap[$namespace] . ':' . $localname;
                    foreach ($values as $value) {
                        if ($value instanceof BNode) {
                            $buffer []= sprintf('<%s rdf:nodeID="%s"/>', $qname, $value);
                        } else if ($value instanceof Literal) {
                            if ($language = $value->getLanguage()) {
                                $attr = sprintf('xml:lang="%s"', htmlspecialchars($language, ENT_QUOTES|ENT_XML1));
                            } else if ($datatype = $value->getDatatype()) {
                                $attr = sprintf('rdf:datatype="%s"', htmlspecialchars($datatype, ENT_QUOTES|ENT_XML1));
                            }
                            $buffer []= sprintf('<%s %s>%s</%s>', $qname, $attr, htmlspecialchars($value, ENT_QUOTES|ENT_XML1), $qname);
                        } else {
                            $buffer []= sprintf('<%s rdf:resource="%s"/>', $qname, htmlspecialchars($value, ENT_QUOTES|ENT_XML1));
                        }
                    }
                }
            }
            $buffer []= '</rdf:Description>';
        }
        $buffer []= '</rdf:RDF>';
        return implode(PHP_EOL, $buffer);
    }

    /**
     * Add a namespace declaration.
     *
     * @param  string $namespaceUri
     * @param  string $prefix
     * @return void
     */
    public function addNamespaceDecl ($namespaceUri, $prefix)
    {
        $this->namespaceDeclMapDefault[$namespace] = $prefix;
    }


    /**
     * Prepare predicate abbreviation.
     *
     * Creates two data structures: self::$namespaceDeclMap which maps
     * namespace URIs to prefixes and self::$uriAbbreviationMap with
     * maps URIs to abbreviations.
     *
     * @todo   Maybe factor out into separate class
     *
     * @param  Graph $graph
     * @return void
     */
    private function createPredicateAbbreviationMaps (Graph $graph)
    {
        $this->namespaceDeclMap = $this->namespaceDeclMapDefault;
        foreach ($graph as $triple) {
            $predicate = $triple[1];
            if (!array_key_exists($predicate, $this->uriAbbreviationMap)) {
                $abbreviation = $this->abbreviate($predicate);
                if (!empty($abbreviation)) {
                    list($namespace, $localname) = $abbreviation;
                    if (!array_key_exists($namespace, $this->namespaceDeclMap)) {
                        $this->namespaceDeclMap[$namespace] = sprintf('ns%d', $this->namespaceDeclCount++);
                    }
                    $this->uriAbbreviationMap[$predicate] = $abbreviation;
                } else {
                    @trigger_error(sprintf('Unable to split URI <%s> into namespace and localname', $predicate), E_USER_WARNING);
                }
            }
        }
    }

    /**
     * Return URI abbreviation.
     *
     * Returns pair (namespace, localname) or false if abbreviation is
     * not possible.
     *
     * @param  string $uri
     * @return array|false
     */
    public function abbreviate ($uri)
    {
        $components = parse_url($uri);
        if (!is_array($components)) {
            return false;
        }
        if (!array_key_exists('scheme', $components) or !array_key_exists('host', $components) or !array_key_exists('path', $components)) {
            return false;
        }
        if (array_key_exists('query', $components)) {
            return false;
        }
        if (array_key_exists('fragment', $components)) {
            return array(substr($uri, 0, 1 + strrpos($uri, '#')), $components['fragment']);
        }
        $segpos = strrpos($uri, '/');
        if ($segpos !== false and $segpos !== strlen($uri) - 1) {
            return array(substr($uri, 0, 1 + $segpos), substr($uri, 1 + $segpos));
        }
        return false;
    }
}