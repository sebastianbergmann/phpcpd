<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @copyright 2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

require_once 'PHPCPD/Clone.php';
require_once 'PHPCPD/CloneMap.php';

/**
 * PHPCPD code analyser.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_Detector
{
    /**
     * @var integer[] List of tokens to ignore
     */
    protected $tokensIgnoreList = array(
      T_INLINE_HTML => TRUE,
      T_COMMENT => TRUE,
      T_DOC_COMMENT => TRUE,
      T_OPEN_TAG => TRUE,
      T_OPEN_TAG_WITH_ECHO => TRUE,
      T_CLOSE_TAG => TRUE,
      T_WHITESPACE => TRUE
    );

    /**
     * @static hash length in bytes
     */
    const BYTES_IN_HASH = 5;

    /**
     * @static number of decimal positions
     * used to shift the file number in composed
     * fileNr...lineNr hash value
     */
    const DECIMAL_OFFSET = 100000;

    /**
     * @var ezcConsoleOutput
     */
    protected $output;

    /**
     * @var string full path incl. file name of the processed code file
     */
    private $processedFile;

    /**
     * @var int minimum number of identical lines considered for match
     */

    private $minimumLines;
    /*
     * @var array list of processed files
     */
    private $fileList;

    /**
     * Constructor.
     *
     * @param ezcConsoleOutput $output
     * @since Method available since Release 1.3.0
     */
    public function __construct(ezcConsoleOutput $output = NULL)
    {
        $this->output = $output;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  array            $files     List of files to process
     * @param  integer          $minLines  Minimum number of identical lines
     * @param  integer          $minTokens Minimum number of identical tokens
     * @return PHPCPD_CloneMap  Map of exact clones found in the list of files
     * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
     */
    public function copyPasteDetection($files, $minLines = 5, $minTokens = 70)
    {
        $this->minimumLines = $minLines;
        $this->fileList     = $files;
        $clonesFound        = new PHPCPD_CloneMap();
        $numLines           = 0;
        $hashes             = array();

        unset($files);

        if ($this->output !== NULL) {
            $bar = new ezcConsoleProgressbar($this->output, count($files));
            print "Processing files\n";
        }

        $numLines = 0;
        foreach ($this->fileList as $fileNr => $this->processedFile) {
            // reset file parsing results from previous run
            $tokenSequence = array();
            $currentSignature      = '';
            $tokenNr               = 0;
            $tokens                = array();
            $lineNr                  = 1;

            // produce syntactical tokens from code file
            $this->tokenizeFile($numLines, $tokens);

            // strip ignored tokens, store relevant token positions
            $count = $this->preprocessTokenList($tokens,
                                                $lineNr,
                                                $currentSignature,
                                                $tokenSequence);
            $firstLine = 0;
            $found     = FALSE;

            if ($count > 0) {
                $tokenRange = $count - $minTokens + 2;
                do {
                    $lineNr = $tokenSequence[$tokenNr];

                    $hash = $this->calculateHash($currentSignature, $tokenNr, $minTokens);
                    if (isset($hashes[$hash])) {
                        $found = TRUE;
                        if ($firstLine === 0) {
                            $firstLine  = $lineNr;
                            $firstHash  = $hash;
                            $firstToken = $tokenNr;
                        }
                    } else {
                        $this->addClone($found,
                                        $firstLine,
                                        $clonesFound,
                                        $hashes[$firstHash],
                                        $lineNr,
                                        $firstToken,
                                        $tokenNr);
                        // store file number
                        $hashes[$hash] = $this->composeValue($fileNr, $lineNr);
                    }

                    $tokenNr++;
                } while ($tokenNr < $tokenRange);
                $this->addClone($found,
                                $firstLine,
                                $clonesFound,
                                $hashes[$firstHash],
                                $lineNr,
                                $firstToken,
                                $tokenNr);
            }

            if ($this->output !== NULL) {
                $bar->advance();
            }
        }

        if ($this->output !== NULL) {
            print "\n\n";
        }

        $clonesFound->setNumLines($numLines);
        return $clonesFound;
    }

    /**
     * almost reverse operation to @see composeValue($fileNr, $line). This routine
     * inverts the packing rule applied by composeValue and returns the file
     * name (instead of its numeric position) and line number.
     *
     * @param  integer $value composed value
     * @return array   name of the file and line number
     */
    private function decomposeValue($value)
    {
        $fileNr = floor($value / self::DECIMAL_OFFSET);
        return array($this->fileList[$fileNr], $value - self::DECIMAL_OFFSET * $fileNr);
    }

    /**
     * composes integer value from file number and line number. This routine
     * shifts the numeric position of the file name in the list by n decimal
     * positions to the left and fills right most zeros with the line number
     * numeric value.
     *
     * @param  integer $fileNr number of the file in the file list array
     * @param  integer $line   number of the line to be composed
     * @return integer "big integer" contaning 2 other integers...
     */
    private function composeValue($fileNr, $line)
    {
        return self::DECIMAL_OFFSET * $fileNr + $line;
    }

    /**
     * unifies the check and insertion logic for discovered clones
     *
     * @param boolean         $found         interim processing flag, upon insertion FALSE value is assigned
     * @param integer         $firstLine     number of the first line matching
     * @param PHPCPD_CloneMap $clonesFound   result object
     * @param integer         $composedValue number containing line number and file number
     * @param integer         $line          current file overall processing line
     * @param integer         $firstToken    number of the first matching token
     * @param integer         $tokenNr       currently processed token number
     */
    private function addClone(&$found, &$firstLine, PHPCPD_CloneMap $clonesFound, $composedValue, $line, $firstToken, $tokenNr)
    {
        if ($found) {
            list($fileA, $firstLineA) = $this->decomposeValue($composedValue);
            if ($line + 1 - $firstLine > $this->minimumLines &&
                ($fileA != $this->processedFile || $firstLineA != $firstLine)) {
                $clone = new PHPCPD_Clone($fileA,
                                          $firstLineA,
                                          $this->processedFile,
                                          $firstLine,
                                          $line + 1 - $firstLine,
                                          $tokenNr + 1 - $firstToken);
                $clonesFound->addClone($clone);
            }
            $found     = FALSE;
            $firstLine = 0;
        }
    }

    /**
     * calculates distinct hash value for given signature
     * 
     * @param bstring $currentSignature
     * @param integer $tokenNr
     * @param integer $minTokens
     * @return bstring first 8 charactes of md5 checksum
     */
    private function calculateHash($currentSignature, $tokenNr, $minTokens)
    {
        $part = substr($currentSignature,
                       $tokenNr   * self::BYTES_IN_HASH,
                       $minTokens * self::BYTES_IN_HASH);
        return substr(md5($part, TRUE), 0, 8);
    }

    /**
     * reads content of the code file, tokenizes the code and returns
     * number of lines in the file as well as syntax tokens.
     *
     * @param string  $fielName full path to the code file
     * @param integer $lineCount number of lines in the file (it's return value!)
     * @param mixed   $fileTokens syntax token array (it's return value!)
     */
    private function tokenizeFile(&$lineCount, &$fileTokens)
    {
        $buffer = file_get_contents($this->processedFile);
        $lineCount += substr_count($buffer, "\n");
        $fileTokens = token_get_all($buffer);
    }

    /**
     * pre-processes the token list, remove tokens matched by ignore list,
     * store token sigantures
     *
     * @param mixed   $tokens           list of tokens, the list is cleared after this call!
     * @param integer $linesCount       number of processed lines counter. it's a return value!
     * @param integer $currentSignature multi-byte token signature with crc32. it's a return value!
     * @param array   $tokenTable    token position tracker. . it's a return value!
     * @return int                      count of scored relevant tokens
     */
    private function preprocessTokenList(&$tokens, &$linesCount, &$currentSignature, &$tokenTable)
    {
        $tokenNr = 0;
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $linesCount += substr_count($token, "\n");
            } else {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    $tokenTable[$tokenNr++] = $linesCount;
                    $currentSignature .= chr($token[0] & 255) . pack('N*', crc32($token[1]));
                }

                $linesCount += substr_count($token[1], "\n");
            }
        }
        // flush the list, unset doesn't work here with call-by-reference
        $tokens = '';
        return count($tokenTable);
    }
}
?>
