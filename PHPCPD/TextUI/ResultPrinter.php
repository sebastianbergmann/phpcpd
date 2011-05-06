<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

/**
 * A ResultPrinter for the TextUI.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_TextUI_ResultPrinter
{
    /**
     * Prints a result set from PHPCPD_Detector::copyPasteDetection().
     *
     * @param PHPCPD_CloneMap $clones
     * @param string          $commonPath
     * @param bool            $verbose
     */
    public function printResult(PHPCPD_CloneMap $clones, $commonPath, $verbose)
    {
        $numClones = count($clones);

        if ($numClones > 0) {
            $buffer = '';
            $files  = array();
            $lines  = 0;

            foreach ($clones as $clone) {
                if (!isset($files[$clone->aFile])) {
                    $files[$clone->aFile] = TRUE;
                }

                if (!isset($files[$clone->bFile])) {
                    $files[$clone->bFile] = TRUE;
                }

                $lines += $clone->size;

                if($verbose) {
                    $buffer .= sprintf(
                      "\n  - %s:%d-%d\n    %s:%d-%d\n",
                      str_replace($commonPath, '', $clone->aFile),
                      $clone->aStartLine,
                      $clone->aStartLine + $clone->size,
                      str_replace($commonPath, '', $clone->bFile),
                      $clone->bStartLine,
                      $clone->bStartLine + $clone->size
                    );
                }
            }

            printf(
              "Found %d exact clones with %d duplicated lines in %d files%s",
              $numClones,
              $lines,
              count($files),
              $verbose ? ":\n".$buffer : ".\n"
            );
        }

        printf(
          "%s%s duplicated lines out of %d total lines of code.\n\n%s\n",
          $numClones > 0 ? "\n" : '',
          $clones->getPercentage(),
          $clones->getNumLines(),
          PHP_Timer::resourceUsage()
        );
    }
}
