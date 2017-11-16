<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD\Detector;

use SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use Symfony\Component\Console\Helper\ProgressBar;

class Detector
{
    /**
     * @var \SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy
     */
    protected $strategy;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar;

    /**
     * @param AbstractStrategy $strategy
     * @param ProgressBar|null $progressBar
     */
    public function __construct(AbstractStrategy $strategy, ProgressBar $progressBar = null)
    {
        $this->strategy    = $strategy;
        $this->progressBar = $progressBar;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param \Iterator|array $files     List of files to process
     * @param int             $minLines  Minimum number of identical lines
     * @param int             $minTokens Minimum number of identical tokens
     * @param bool            $fuzzy
     *
     * @return CodeCloneMap Map of exact clones found in the list of files
     */
    public function copyPasteDetection($files, $minLines = 5, $minTokens = 70, $fuzzy = false)
    {
        $result = new CodeCloneMap;

        foreach ($files as $file) {
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
