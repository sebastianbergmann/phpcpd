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
use Symfony\Component\Console\Output\OutputInterface;

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
        $verbose = $output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL;

        if (\count($clones) > 0) {
            $output->write(
                \sprintf(
                    'Found %d clones with %d duplicated lines in %d files:' . PHP_EOL . PHP_EOL,
                    \count($clones),
                    $clones->getNumberOfDuplicatedLines(),
                    $clones->getNumberOfFilesWithClones()
                )
            );
        }

        foreach ($clones as $clone) {
            $firstOccurrence = true;

            foreach ($clone->getFiles() as $file) {
                $output->writeln(
                    \sprintf(
                        '  %s%s:%d-%d',
                        $firstOccurrence ? '- ' : '  ',
                        $file->getName(),
                        $file->getStartLine(),
                        $file->getStartLine() + $clone->getSize()
                    )
                );

                $firstOccurrence = false;
            }

            if ($verbose) {
                $output->write(PHP_EOL . $clone->getLines('    '));
            }

            $output->writeln('');
        }

        $output->write(
            \sprintf(
                "%s duplicated lines out of %d total lines of code.\n\n",
                $clones->getPercentage(),
                $clones->getNumLines()
            )
        );
    }
}
