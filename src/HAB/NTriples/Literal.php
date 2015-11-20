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

/**
 * A literal.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2015 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
class Literal
{
    /**
     * Literal value.
     *
     * @var string
     */
    private $value;

    /**
     * Literal langauge, if any.
     *
     * @var string
     */
    private $language;

    /**
     * Literal datatype, if any.
     *
     * @var string
     */
    private $datatype;

    /**
     * Constructor.
     *
     * @param  string $value
     * @param  string $language
     * @param  string $datatype
     * @return void
     */
    public function __construct ($value, $language = null, $datatype = null)
    {
        $this->value = $value;
        $this->language = $language;
        $this->datatype = $datatype;
    }

    /**
     * Return value.
     *
     * @return string
     */
    public function getValue ()
    {
        return $this->value;
    }

    /**
     * Return language.
     *
     * @return string
     */
    public function getLanguage ()
    {
        return $this->language;
    }

    /**
     * Return datatype.
     *
     * @return string
     */
    public function getDatatype ()
    {
        return $this->datatype;
    }

    /**
     * Return string representation.
     *
     * @return string
     */
    public function __toString ()
    {
        return $this->getValue();
    }
}