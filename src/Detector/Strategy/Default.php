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

namespace SebastianBergmann\PHPCPD\Detector\Strategy
{
    use SebastianBergmann\PHPCPD\CodeClone;
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
         * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
         */
        public function processFile($file, $minLines, $minTokens, CodeCloneMap $result)
        {
            $buffer                = file_get_contents($file);
            $currentTokenPositions = array();
            $currentSignature      = '';
            $tokens                = token_get_all($buffer);
            $tokenNr               = 0;
            $line                  = 1;

            $result->setNumLines(
              $result->getNumLines() + substr_count($buffer, "\n")
            );

            unset($buffer);

            foreach (array_keys($tokens) as $key) {
                $token = $tokens[$key];

                if (is_string($token)) {
                    $line += substr_count($token, "\n");
                } else {
                    if (!isset($this->tokensIgnoreList[$token[0]])) {
                        $currentTokenPositions[$tokenNr++] = $line;

                        $currentSignature .= chr(
                          $token[0] & 255) . pack('N*', crc32($token[1])
                        );
                    }

                    $line += substr_count($token[1], "\n");
                }
            }

            $count     = count($currentTokenPositions);
            $firstLine = 0;
            $found     = FALSE;
            $tokenNr   = 0;

            if ($count > 0) {
                do {
                    $line = $currentTokenPositions[$tokenNr];

                    $hash = substr(
                      md5(
                        substr(
                          $currentSignature, $tokenNr * 5,
                          $minTokens * 5
                        ),
                        TRUE
                      ),
                      0,
                      8
                    );

                    if (isset($this->hashes[$hash])) {
                        $found = TRUE;

                        if ($firstLine === 0) {
                            $firstLine  = $line;
                            $firstHash  = $hash;
                            $firstToken = $tokenNr;
                        }
                    } else {
                        if ($found) {
                            $fileA      = $this->hashes[$firstHash][0];
                            $firstLineA = $this->hashes[$firstHash][1];

                            if ($line + 1 - $firstLine > $minLines &&
                                ($fileA != $file ||
                                 $firstLineA != $firstLine)) {
                                $result->addClone(
                                  new CodeClone(
                                    $fileA,
                                    $firstLineA,
                                    $file,
                                    $firstLine,
                                    $line + 1 - $firstLine,
                                    $tokenNr + 1 - $firstToken
                                  )
                                );
                            }

                            $found     = FALSE;
                            $firstLine = 0;
                        }

                        $this->hashes[$hash] = array($file, $line);
                    }

                    $tokenNr++;
                } while ($tokenNr <= count($currentTokenPositions) - 1);
            }

            if ($found) {
                $fileA      = $this->hashes[$firstHash][0];
                $firstLineA = $this->hashes[$firstHash][1];

                if ($line + 1 - $firstLine > $minLines &&
                    ($fileA != $file || $firstLineA != $firstLine)) {
                    $result->addClone(
                      new CodeClone(
                        $fileA,
                        $firstLineA,
                        $file,
                        $firstLine,
                        $line + 1 - $firstLine,
                        $tokenNr + 1 - $firstToken
                      )
                    );
                }

                $found = FALSE;
            }
        }
    }
}
