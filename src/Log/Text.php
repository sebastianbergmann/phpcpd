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
use function printf;
use SebastianBergmann\PHPCPD\CodeCloneMap;

final class Text
{
    public function printResult(CodeCloneMap $clones, bool $verbose): void
    {
        if (count($clones) > 0) {
            printf(
                'Found %d clones with %d duplicated lines in %d files:' . PHP_EOL . PHP_EOL,
                count($clones),
                $clones->numberOfDuplicatedLines(),
                $clones->numberOfFilesWithClones()
            );
        }

        foreach ($clones as $clone) {
            $firstOccurrence = true;

            foreach ($clone->files() as $file) {
                printf(
                    '  %s%s:%d-%d%s' . PHP_EOL,
                    $firstOccurrence ? '- ' : '  ',
                    $file->name(),
                    $file->startLine(),
                    $file->startLine() + $clone->numberOfLines(),
                    $firstOccurrence ? ' (' . $clone->numberOfLines() . ' lines)' : ''
                );

                $firstOccurrence = false;
            }

            if ($verbose) {
                print PHP_EOL . $clone->lines('    ');
            }

            print PHP_EOL;
        }

        if ($clones->isEmpty()) {
            print 'No clones found.' . PHP_EOL . PHP_EOL;

            return;
        }

        printf(
            '%s duplicated lines out of %d total lines of code.' . PHP_EOL .
            'Average size of duplication is %d lines, largest clone has %d of lines' . PHP_EOL . PHP_EOL,
            $clones->percentage(),
            $clones->numberOfLines(),
            $clones->averageSize(),
            $clones->largestSize()
        );
    }
}
