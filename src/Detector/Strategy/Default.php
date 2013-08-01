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
 * @since     File available since Release 1.4.0
 */

namespace SebastianBergmann\PHPCPD\Detector\Strategy;

use SebastianBergmann\PHPCPD\CodeClone;
use SebastianBergmann\PHPCPD\CodeCloneFile;
use SebastianBergmann\PHPCPD\CodeCloneMap;

/**
 * Default strategy for detecting code clones.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.4.0
 */
class DefaultStrategy extends AbstractStrategy
{
    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  string       $file
     * @param  integer      $minLines
     * @param  integer      $minTokens
     * @param  CodeCloneMap $result
     * @param  boolean      $fuzzy
     * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
     */
    public function processFile($file, $minLines, $minTokens, CodeCloneMap $result, $fuzzy = false)
    {
        $buffer                    = file_get_contents($file);
        $currentTokenPositions     = array();
        $currentTokenRealPositions = array();
        $currentSignature          = '';
        $tokens                    = token_get_all($buffer);
        $tokenNr                   = 0;
        $lastTokenLine             = 0;

        $result->setNumLines(
            $result->getNumLines() + substr_count($buffer, "\n")
        );

        unset($buffer);

        foreach (array_keys($tokens) as $key) {
            $token = $tokens[$key];

            if (is_array($token)) {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    if ($tokenNr == 0) {
                        $currentTokenPositions[$tokenNr] = $token[2] - $lastTokenLine;
                    } else {
                        $currentTokenPositions[$tokenNr] = $currentTokenPositions[$tokenNr - 1] +
                                                           $token[2] - $lastTokenLine;
                    }

                    $currentTokenRealPositions[$tokenNr++] = $token[2];

                    if ($fuzzy && $token[0] == T_VARIABLE) {
                        $token[1] = 'variable';
                    }

                    $currentSignature .= chr($token[0] & 255) .
                                         pack('N*', crc32($token[1]));
                }

                $lastTokenLine = $token[2];
            }
        }

        $count         = count($currentTokenPositions);
        $firstLine     = 0;
        $firstRealLine = 0;
        $found         = false;
        $tokenNr       = 0;

        while ($tokenNr <= $count - $minTokens) {
            $line     = $currentTokenPositions[$tokenNr];
            $realLine = $currentTokenRealPositions[$tokenNr];

            $hash = substr(
                md5(
                    substr(
                        $currentSignature,
                        $tokenNr * 5,
                        $minTokens * 5
                    ),
                    true
                ),
                0,
                8
            );

            if (isset($this->hashes[$hash])) {
                $found = true;

                if ($firstLine === 0) {
                    $firstLine     = $line;
                    $firstRealLine = $realLine;
                    $firstHash     = $hash;
                    $firstToken    = $tokenNr;
                }
            } else {
                if ($found) {
                    $fileA        = $this->hashes[$firstHash][0];
                    $firstLineA   = $this->hashes[$firstHash][1];
                    $lastToken    = ($tokenNr - 1) + $minTokens - 1;
                    $lastLine     = $currentTokenPositions[$lastToken];
                    $lastRealLine = $currentTokenRealPositions[$lastToken];
                    $numLines     = $lastLine + 1 - $firstLine;
                    $realNumLines = $lastRealLine +1 - $firstRealLine;

                    if ($numLines >= $minLines &&
                        ($fileA != $file ||
                         $firstLineA != $firstRealLine)) {
                        $result->addClone(
                            new CodeClone(
                                new CodeCloneFile($fileA, $firstLineA),
                                new CodeCloneFile($file, $firstRealLine),
                                $realNumLines,
                                $lastToken + 1 - $firstToken
                            )
                        );
                    }

                    $found     = false;
                    $firstLine = 0;
                }

                $this->hashes[$hash] = array($file, $realLine);
            }

            $tokenNr++;
        }

        if ($found) {
            $fileA        = $this->hashes[$firstHash][0];
            $firstLineA   = $this->hashes[$firstHash][1];
            $lastToken    = ($tokenNr - 1) + $minTokens - 1;
            $lastLine     = $currentTokenPositions[$lastToken];
            $lastRealLine = $currentTokenRealPositions[$lastToken];
            $numLines     = $lastLine + 1 - $firstLine;
            $realNumLines = $lastRealLine +1 - $firstRealLine;

            if ($numLines >= $minLines &&
                ($fileA != $file || $firstLineA != $firstRealLine)) {
                $result->addClone(
                    new CodeClone(
                        new CodeCloneFile($fileA, $firstLineA),
                        new CodeCloneFile($file, $firstRealLine),
                        $realNumLines,
                        $lastToken + 1 - $firstToken
                    )
                );
            }

            $found = false;
        }
    }
}
