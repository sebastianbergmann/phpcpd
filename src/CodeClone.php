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

use SebastianBergmann\PHPCPD\CodeCloneFile;

/**
 * Represents an exact code clone.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.1.0
 */
class CodeClone
{
    /**
     * @var integer Size of the clone (lines)
     */
    private $size;

    /**
     * @var integer Size of the clone (tokens)
     */
    private $tokens;

    /**
     * @var CodeCloneFile[] Files with this code clone
     */
    private $files = array();

    /**
     * @var string Unique ID of Code Duplicate Fragment
     */
    private $id;

    /**
     * @var Lines of the clone
     */
    private $lines = '';

    /**
     * Constructor.
     *
     * @param CodeCloneFile $fileA
     * @param CodeCloneFile $fileB
     * @param integer       $size
     * @param integer       $tokens
     */
    public function __construct(CodeCloneFile $fileA, CodeCloneFile $fileB, $size, $tokens)
    {
        $this->addFile($fileA);
        $this->addFile($fileB);

        $this->size   = $size;
        $this->tokens = $tokens;
        $this->id     = md5($this->getLines());
    }

    /**
     * Add file with clone
     *
     * @param CodeCloneFile $file
     */
    public function addFile(CodeCloneFile $file)
    {
        $id = $file->getId();

        if (!isset($this->files[$id])) {
            $this->files[$id] = $file;
        }
    }

    /**
     * Get files with clone
     *
     * @return CodeCloneFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the lines of the clone.
     *
     * @param  string $prefix
     * @return string The lines of the clone
     */
    public function getLines($prefix = '')
    {
        $file = current($this->files);

        if (empty($this->lines)) {
            $lines = array_slice(
                file($file->getName()),
                $file->getStartLine() - 1,
                $this->size
            );

            $indent = array();

            foreach ($lines as &$line) {
                $line    = rtrim($line, " \t\0\x0B");
                $line    = str_replace("\t", "    ", $line);
                $_indent = strlen($line) - strlen(ltrim($line));

                if ($_indent > 1) {
                    $indent[] = $_indent;
                }
            }

            $indent = empty($indent) ? 0 : min($indent);

            if ($indent > 0) {
                foreach ($lines as &$line) {
                    if (strlen($line > 1)) {
                        $line = $prefix . substr($line, $indent);
                    }
                }
            }

            $this->lines = join('', $lines);
        }

        return $this->lines;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return integer
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}
