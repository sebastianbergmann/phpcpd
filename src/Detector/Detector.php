<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD\Detector;

use SebastianBergmann\PHPCPD\CodeCloneMap;
use SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy;
use Symfony\Component\Console\Helper\ProgressBar;

final class Detector
{
    /**
     * @var \SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy
     */
    private $strategy;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    private $progressBar;

    public function __construct(AbstractStrategy $strategy, ProgressBar $progressBar = null)
    {
        $this->strategy    = $strategy;
        $this->progressBar = $progressBar;
    }

    public function copyPasteDetection(iterable $files, int $minLines = 5, int $minTokens = 70, bool $fuzzy = false): CodeCloneMap
    {
        $result = new CodeCloneMap;

        foreach ($files as $file) {
            if (empty($file)) {
                continue;
            }

            $this->strategy->processFile(
                $file,
                $minLines,
                $minTokens,
                $result,
                $fuzzy
            );

            if ($this->progressBar !== null) {
                $this->progressBar->advance();
            }
        }

        return $result;
    }
}
