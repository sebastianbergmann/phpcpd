<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

/**
 *
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_Detector
{
    protected static $CPD_IGNORE_LIST = array(
      T_INLINE_HTML,
      T_COMMENT,
      T_DOC_COMMENT,
      T_OPEN_TAG,
      T_OPEN_TAG_WITH_ECHO,
      T_CLOSE_TAG,
      T_WHITESPACE
    );

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  array   $files
     * @param  integer $minLines
     * @param  integer $minTokens
     * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
     */
    public static function copyPasteDetection($files, $minLines, $minTokens)
    {
        $clones = array();
        $hashes = array();

        foreach ($files as $file) {
            $file                  = $file->getPathName();
            $currentTokenPositions = array();
            $currentSignature      = '';
            $lines                 = file($file);
            $tokens                = token_get_all(file_get_contents($file));
            $tokenNr               = 0;
            $line                  = 1;

            foreach (array_keys($tokens) as $key) {
                $token = $tokens[$key];

                if (is_string($token)) {
                    $line += substr_count($token, "\n");
                } else {
                    if (!in_array($token[0], self::$CPD_IGNORE_LIST)) {
                        $currentTokenPositions[$tokenNr++] = $line;
                        $currentSignature .= chr($token[0] & 255) . pack('N*', crc32($token[1]));
                    }

                    $line += substr_count($token[1], "\n");
                }
            }

            $tokenNr   = 0;
            $firstLine = 0;
            $found     = FALSE;

            if (count($currentTokenPositions) > 0) {
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

                    if (isset($hashes[$hash])) {
                        $found = TRUE;

                        if ($firstLine === 0) {
                            $firstLine  = $line;
                            $firstHash  = $hash;
                            $firstToken = $tokenNr;
                        }
                    } else {
                        if ($found) {
                            $fileA      = $hashes[$firstHash][0];
                            $firstLineA = $hashes[$firstHash][1];

                            if ($line + 1 - $firstLine > $minLines &&
                                ($fileA != $file || $firstLineA != $firstLine)) {
                                $clones[] = array(
                                  'fileA'      => $fileA,
                                  'firstLineA' => $firstLineA,
                                  'fileB'      => $file,
                                  'firstLineB' => $firstLine,
                                  'numLines'   => $line    + 1 - $firstLine,
                                  'numTokens'  => $tokenNr + 1 - $firstToken
                                );
                            }

                            $found     = FALSE;
                            $firstLine = 0;
                        }

                        $hashes[$hash] = array($file, $line);
                    }

                    $tokenNr++;
                } while ($tokenNr <= (count($currentTokenPositions) - $minTokens) + 1);
            }

            if ($found) {
                $fileA      = $hashes[$firstHash][0];
                $firstLineA = $hashes[$firstHash][1];

                if ($line + 1 - $firstLine > $minLines &&
                    ($fileA != $file || $firstLineA != $firstLine)) {
                    $clones[] = array(
                      'fileA'      => $fileA,
                      'firstLineA' => $firstLineA,
                      'fileB'      => $file,
                      'firstLineB' => $firstLine,
                      'numLines'   => $line    + 1 - $firstLine,
                      'numTokens'  => $tokenNr + 1 - $firstToken
                    );
                }

                $found = FALSE;
            }
        }

        return $clones;
    }
}
?>
