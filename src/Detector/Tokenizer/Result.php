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
 * @since     File available since Release 2.0.0
 */

namespace SebastianBergmann\PHPCPD\Detector\Tokenizer;

/**
 * Value object that represents the output of tokenizing a file.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 2.0.0
 */
class Result
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * @var integer
     */
    private $numberOfLines;

    /**
     * Constructor.
     *
     * @param array   $tokens           Array of tokens, in the same format
     *                                  as token_get_all() returns them.
     * @param integer $numberOfLines  The number of lines in the read file.
     */
    public function __construct(array $tokens, $numberOfLines)
    {
        $this->tokens = $tokens;
        $this->numberOfLines = $numberOfLines;
    }

    /**
     * Returns the tokens that were read from the file.
     *
     * @return array        Array of tokens, in the same format
     *                      as token_get_all() returns them.
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns the number of lines in the file.
     *
     * @return integer
     */
    public function getNumberOfLines()
    {
        return $this->numberOfLines;
    }
}
