<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 1.1.0
 */

namespace SebastianBergmann\PHPCPD;

use SebastianBergmann\PHPCPD\CodeClone;

/**
 * A map of exact clones.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.1.0
 */
class CodeCloneMap implements \Countable, \Iterator
{
    /**
     * @var CodeClone[] The clones in the clone map
     */
    protected $clones = array();

    /**
     * @var CodeClone[] The clones in the clone map, stored by ID
     */
    protected $clonesById = array();

    /**
     * @var integer Current position while iterating the clone map
     */
    protected $position = 0;

    /**
     * @var integer Number of duplicate lines in the clone map
     */
    protected $numDuplicateLines = 0;

    /**
     * @var integer Number of lines analyzed
     */
    protected $numLines = 0;

    /**
     * Adds a clone to the map.
     *
     * @param CodeClone $clone
     */
    public function addClone(CodeClone $clone)
    {
        $id = $clone->getId();

        if (!isset($this->clonesById[$id])) {
            $this->clones[]        = $clone;
            $this->clonesById[$id] = $clone;
        } else {
            $existClone = $this->clonesById[$id];

            foreach ($clone->getFiles() as $file) {
                $existClone->addFile($file);
            }
        }

        $this->numDuplicateLines += $clone->getSize();
    }

    /**
     * Returns the clones stored in this map.
     *
     * @return CodeClone[]
     */
    public function getClones()
    {
        return $this->clones;
    }

    /**
     * Returns the percentage of duplicated code lines in the project.
     *
     * @return string
     */
    public function getPercentage()
    {
        if ($this->numLines > 0) {
            $percent = ($this->numDuplicateLines / $this->numLines) * 100;
        } else {
            $percent = 100;
        }

        return sprintf('%01.2F%%', $percent);
    }

    /**
     * Returns the number of lines analyzed.
     *
     * @return integer
     */
    public function getNumLines()
    {
        return $this->numLines;
    }

    /**
     * Sets the number of physical source code lines in the project.
     *
     * @param integer $numLines
     */
    public function setNumLines($numLines)
    {
        $this->numLines = $numLines;
    }

    /**
     * Returns the number of clones stored in this map.
     */
    public function count()
    {
        return count($this->clones);
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Checks if there is a current element after calls to rewind() or next().
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->position < count($this->clones);
    }

    /**
     * Returns the key of the current element.
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Returns the current element.
     *
     * @return CodeClone
     */
    public function current()
    {
        return $this->clones[$this->position];
    }

    /**
     * Moves forward to next element.
     */
    public function next()
    {
        $this->position++;
    }
}
