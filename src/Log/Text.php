<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Log;

use const PHP_EOL;
use function count;
use function sprintf;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use Symfony\Component\Console\Output\OutputInterface;

final class Text
{
    public function printResult(OutputInterface $output, CodeCloneMap $clones): void
    {
        $verbose = $output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL;

        if (count($clones) > 0) {
            $output->write(
                sprintf(
                    'Found %d clones with %d duplicated lines in %d files:' . PHP_EOL . PHP_EOL,
                    count($clones),
                    $clones->numberOfDuplicatedLines(),
                    $clones->numberOfFilesWithClones()
                )
            );
        }

        foreach ($clones as $clone) {
            $firstOccurrence = true;

            foreach ($clone->files() as $file) {
                $output->writeln(
                    sprintf(
                        '  %s%s:%d-%d%s',
                        $firstOccurrence ? '- ' : '  ',
                        $file->name(),
                        $file->startLine(),
                        $file->startLine() + $clone->numberOfLines(),
                        $firstOccurrence ? ' (' . $clone->numberOfLines() . ' lines)' : ''
                    )
                );

                $firstOccurrence = false;
            }

            if ($verbose) {
                $output->write(PHP_EOL . $clone->lines('    '));
            }

            $output->writeln('');
        }

        if ($clones->isEmpty()) {
            $output->write("No clones found.\n\n");

            return;
        }

        $output->write(
            sprintf(
                "%s duplicated lines out of %d total lines of code.\n" .
                "Average size of duplication is %d lines, largest clone has %d of lines\n\n",
                $clones->percentage(),
                $clones->numberOfLines(),
                $clones->averageSize(),
                $clones->largestSize()
            )
        );
    }
}
