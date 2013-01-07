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

namespace SebastianBergmann\PHPCPD
{
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
         * @var string Name of the first file
         */
        public $aFile;

        /**
         * @var integer Start line in the first file
         */
        public $aStartLine;

        /**
         * @var string Name of the second file
         */
        public $bFile;

        /**
         * @var integer Start line in the second file
         */
        public $bStartLine;

        /**
         * @var integer Size of the clone (lines)
         */
        public $size;

        /**
         * @var integer Size of the clone (tokens)
         */
        public $tokens;

        /**
         * @var Lines of the clone
         */
        protected $lines = '';

        /**
         * Constructor.
         *
         * @param string  $aFile       Name of the first file
         * @param integer $aStartLine  Start line in the first file
         * @param string  $bFile       Name of the second file
         * @param integer $bStartLine  Start line in the second file
         * @param integer $size        Size of the clone (lines)
         * @param integer $tokens      Size of the clone (tokens)
         */
        public function __construct($aFile, $aStartLine, $bFile, $bStartLine, $size, $tokens)
        {
            $this->aFile      = $aFile;
            $this->aStartLine = $aStartLine;
            $this->bFile      = $bFile;
            $this->bStartLine = $bStartLine;
            $this->size       = $size;
            $this->tokens     = $tokens;
        }

        /**
         * Returns the lines of the clone.
         *
         * @param  string $prefix
         * @return string The lines of the clone
         */
        public function getLines($prefix = '')
        {
            if (empty($this->lines)) {
                $lines = array_slice(
                  file($this->aFile), $this->aStartLine - 1, $this->size
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
    }
}
