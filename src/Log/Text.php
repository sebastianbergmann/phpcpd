<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD\Log;

use SebastianBergmann\PHPCPD\CodeCloneMap;
use SebastianBergmann\PHPCPD\CodeClone;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A ResultPrinter for the TextUI.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright Sebastian Bergmann <sebastian@phpunit.de>
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
                        "\t%s:%d-%d\n ",
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
