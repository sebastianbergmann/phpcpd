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

namespace SebastianBergmann\PHPCPD\Log;

use SebastianBergmann\PHPCPD\CodeCloneMap;
use SebastianBergmann\PHPCPD\CodeClone;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A ResultPrinter for the TextUI.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 2.0.0
 */
class Text
{
    /**
     * Prints a result set from Detector::copyPasteDetection().
     *
     * @param OutputInterface $output
     * @param CodeCloneMap    $clones
     */
    public function printResult(OutputInterface $output, CodeCloneMap $clones)
    {
        $numClones = count($clones);
        $verbose   = $output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL;

        if ($numClones > 0) {
            $buffer = '';
            $files  = array();
            $lines  = 0;

            foreach ($clones as $clone) {
                foreach ($clone->getFiles() as $file) {
                    $filename = $file->getName();

                    if (!isset($files[$filename])) {
                        $files[$filename] = true;
                    }
                }

                $lines  += $clone->getSize() * (count($clone->getFiles()) - 1);
                $buffer .= "\n  -";

                foreach ($clone->getFiles() as $file) {
                    $buffer .= sprintf(
                        "\r\t%s:%d-%d\n ",
                        $file->getName(),
                        $file->getStartLine(),
                        $file->getStartLine() + $clone->getSize()
                    );
                }

                if ($verbose) {
                    $buffer .= "\n" . $clone->getLines('      ');
                }
            }

            $output->write(
                sprintf(
                    "Found %d exact clones with %d duplicated lines in %d files:\n%s",
                    $numClones,
                    $lines,
                    count($files),
                    $buffer
                )
            );
        }

        $output->write(
            sprintf(
                "%s%s duplicated lines out of %d total lines of code.\n\n",
                $numClones > 0 ? "\n" : '',
                $clones->getPercentage(),
                $clones->getNumLines()
            )
        );
    }
}
