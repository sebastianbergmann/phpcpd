<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
 * @copyright Sebastian Bergmann <sebastian@phpunit.de>
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
