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

namespace HAB\NTriples;

use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Graph.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Graph implements Countable, IteratorAggregate
{
    /**
     * Triples indexed by subject key and predicate.
     *
     * @var array
     */
    private $index = array();

    /**
     * Subjects indexed by key.
     *
     * @var array
     */
    private $subjects = array();

    /**
     * Triples.
     *
     * @var array
     */
    private $triples = array();

    /**
     * Subject key counter.
     *
     * @var integer
     */
    private $subjectKeyCounter = 0;

    /**
     * Graph name.
     *
     * @var string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param  string $name
     * @return void
     */
    public function __construct ($name = null)
    {
        $this->setName($name);
    }

    /**
     * Add a triple.
     *
     * @param  string|BNode         $subject
     * @param  string               $predicate
     * @param  string|BNode|Literal $object
     * @return void
     */
    public function add ($subject, $predicate, $object)
    {
        $key = array_search($subject, $this->subjects, true);
        if ($key === false) {
            $key = ++$this->subjectKeyCounter;
            $this->subjects[$key] = $subject;
        }
        $this->index[$key][$predicate] []= $object;
        $this->triples []= array($subject, $predicate, $object);
    }

    /**
     * Return distinct subjects.
     *
     * @return array
     */
    public function subjects ()
    {
        return $this->subjects;
    }

    /**
     * Return all properties of subject.
     *
     * Returns an array of the following form:
     *
     * array(PREDICATE => array(VALUE, VALUE, ...))
     *
     * @param  string|BNode $subject
     * @return array
     */
    public function properties ($subject)
    {
        $key = array_search($subject, $this->subjects, true);
        if ($key === false) {
            return array();
        }
        return $this->index[$key];
    }

    /**
     * Merge another graph into this graph.
     *
     * @param  Graph $other
     * @return void
     */
    public function merge (Graph $other)
    {
        if ($other !== $this) {
            foreach ($other as $triple) {
                call_user_func_array(array($this, 'add'), $triple);
            }
        }
    }

    /**
     * Return new graph containing selected triples.
     *
     * @param  callable $selector
     * @param  string   $name
     * @return Graph
     */
    public function select ($selector, $name = null)
    {
        $graph = new Graph($name);
        foreach (array_filter($this->triples, $selector) as $triple) {
            call_user_func_array(array($graph, 'add'), $triple);
        }
        return $graph;
    }

    /**
     * Set graph name.
     *
     * @param  string $name
     * @return void
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

    /**
     * Return graph name.
     *
     * @return string|null
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator ()
    {
        return new ArrayIterator($this->triples);
    }

    /**
     * {@inheritDoc}
     */
    public function count ()
    {
        return count($this->triples);
    }
}