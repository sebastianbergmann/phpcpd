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

final class Detector
{
    /**
     * @var AbstractStrategy
     */
    private $strategy;

    public function __construct(AbstractStrategy $strategy)
    {
        $this->strategy = $strategy;
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
        }

        return $result;
    }
}
