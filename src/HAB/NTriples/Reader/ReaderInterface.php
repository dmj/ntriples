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

namespace HAB\NTriples\Reader;

use RuntimeException;

/**
 * Interface of a Reader.
 *
 * @author    David Maus <maus@hab.de>
 * @copyright (c) 2017 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */
interface ReaderInterface
{
    /**
     * Open reader to read from given URI.
     *
     * @param  string $uri
     * @return void
     */
    public function open ($uri);

    /**
     * Read from handle and return triple.
     *
     * Returns false if the input stream is exhausted.
     *
     * @return array|false
     */
    public function read ();

    /**
     * Close reader.
     *
     * @return void
     */
    public function close ();

    /**
     * Rewind input stream.
     *
     * @throws RuntimeException Cannot rewind input stream
     *
     * @return void
     */
    public function rewind ();

}