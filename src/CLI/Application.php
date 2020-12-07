<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD;

use const PHP_EOL;
use function count;
use function printf;
use SebastianBergmann\FileIterator\Facade;
use SebastianBergmann\PHPCPD\Detector\Detector;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use SebastianBergmann\PHPCPD\Log\PMD;
use SebastianBergmann\PHPCPD\Log\Text;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use SebastianBergmann\Version;

final class Application
{
    private const VERSION = '6.0.3';

    public function run(array $argv): int
    {
        $this->printVersion();

        try {
            $arguments = (new ArgumentsBuilder)->build($argv);
        } catch (Exception $e) {
            print PHP_EOL . $e->getMessage() . PHP_EOL;

            return 1;
        }

        if ($arguments->version()) {
            return 0;
        }

        print PHP_EOL;

        if ($arguments->help()) {
            $this->help();

            return 0;
        }

        $files = (new Facade)->getFilesAsArray(
            $arguments->directories(),
            $arguments->suffixes(),
            '',
            $arguments->exclude()
        );

        if (empty($files)) {
            print 'No files found to scan' . PHP_EOL;

            return 1;
        }

        $strategy = new DefaultStrategy;

        $timer = new Timer;
        $timer->start();

        $clones = (new Detector($strategy))->copyPasteDetection(
            $files,
            $arguments->linesThreshold(),
            $arguments->tokensThreshold(),
            $arguments->fuzzy()
        );

        (new Text)->printResult($clones, $arguments->verbose());

        if ($arguments->pmdCpdXmlLogfile()) {
            (new PMD($arguments->pmdCpdXmlLogfile()))->processClones($clones);
        }

        print (new ResourceUsageFormatter)->resourceUsage($timer->stop()) . PHP_EOL;

        return count($clones) > 0 ? 1 : 0;
    }

    private function printVersion(): void
    {
        printf(
            'phpcpd %s by Sebastian Bergmann.' . PHP_EOL,
            (new Version(self::VERSION, dirname(__DIR__)))->getVersion()
        );
    }

    private function help(): void
    {
        print <<<'EOT'
Usage:
  phpcpd [options] <directory>

Options for selecting files:

  --suffix <suffix> Include files with names ending in <suffix> in the analysis
                    (default: .php; can be given multiple times)
  --exclude <path>  Exclude files with <path> in their path from the analysis
                    (can be given multiple times)

Options for analysing files:

  --fuzzy           Fuzz variable names
  --min-lines <N>   Minimum number of identical lines (default: 5)
  --min-tokens <N>  Minimum number of identical tokens (default: 70)

Options for report generation:

  --log-pmd <file>  Write log in PMD-CPD XML format to <file>

EOT;
    }
}
